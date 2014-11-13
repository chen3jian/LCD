<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 脚印
 * Date: 14-2-14
 * Time: 上午10:00
 * To change this template use File | Settings | File Templates.
 */

namespace Lcd\View;

use Lcd\Core\Config;
use Lcd\Network\Request;
use Lcd\View\Engine\TagLib;

class Template {
    private static $_tLib = array();

    /**
     * 模板页面中引入的标签库列表
     * @var array
     */
    protected $tagLib = array();

    /**
     * 模板变量
     * @var array
     */
    public $config = array(
        'taglib_begin'=>'',
        'taglib_end'=>'',
        'tmpl_begin'=>'',
        'tmpl_end'=>'',

        'TAGLIB_LOAD'=>'',
        'TAGLIB_PRE_LOAD'=>'',
        'TAGLIB_BUILD_IN'=>'',
        'TMPL_DENY_PHP'=>'',
        'TMPL_L_DELIM'=>'',
        'TMPL_R_DELIM'=>'',
        'TMPL_DENY_FUNC_LIST'=>'',
        'TMPL_VAR_IDENTIFY',
    );

    /**
     * @var array
     */
    private $literal = array();

    /**
     * 架构函数
     * @access public
     */
    public function __construct(){
        $this->config = Config::block('View');
        $this->config['tmpl_begin'] = $this->stripPreg($this->config['TMPL_L_DELIM']);
        $this->config['tmpl_end'] = $this->stripPreg($this->config['TMPL_R_DELIM']);
        $this->config['taglib_begin'] = $this->stripPreg($this->config['TAGLIB_BEGIN']);
        $this->config['taglib_end'] = $this->stripPreg($this->config['TAGLIB_END']);
        unset($this->config['TAGLIB_BEGIN'],$this->config['TAGLIB_END']);
    }

    /**
     * 配置标签转义
     * @param string $str
     * @return string
     */
    private function stripPreg($str) {
        return str_replace(
            array('{','}','(',')','|','[',']','-','+','*','.','^','?'),
            array('\{','\}','\(','\)','\|','\[','\]','\-','\+','\*','\.','\^','\?'),
            $str);
    }

    /**
     * 处理模板
     * @param string $templateCacheFile
     * @return void
     * @throws \Exception
     */
    public function parseTemplate($templateCacheFile) {
        $templateFile = str_replace(TPL_CACHE_PATH . Request::$module . DS, MODULE_PATH . Request::$module . DS . 'Template' . DS, $templateCacheFile);
        if(is_file($templateFile)) {
            // 读取模板文件内容
            $tmpContent =  file_get_contents($templateFile);
        } else {
            throw new \Exception("$templateFile 模板文件不存在");
        }

        // 编译模板内容
        $tmpContent = $this->compiler($tmpContent);

        mk_dir(dirname($templateCacheFile));

        if(false === file_put_contents($templateCacheFile, $tmpContent)) {
            throw new \Exception("$templateCacheFile 文件写入错误");
        }
    }

    /**
     * 编译模板文件内容
     * @access protected
     * @param mixed $tmpContent 模板内容
     * @return string
     */
    protected function compiler($tmpContent) {
        //模板解析
        $tmpContent =  $this->parse($tmpContent);
        // 还原被替换的Literal标签
        $tmpContent =  preg_replace_callback('/<!--###literal(\d+)###-->/is', array($this, 'restoreLiteral'), $tmpContent);
        // 添加安全代码
        $tmpContent =  "<?php if (!defined('LCD_PATH')) exit();?>\r\n" . $tmpContent;
        // 优化生成的php代码
        $tmpContent = str_replace('?><?php', '', $tmpContent);
        // 模版编译过滤标签
        return $this->templateContentReplace($tmpContent);
    }

    /**
     * 模板内容替换
     * @access protected
     * @param string $content 模板内容
     * @return string
     */
    protected function templateContentReplace($content) {
        // 系统默认的特殊变量替换
        $replace =  array(
//            '__ROOT__' => __ROOT__,
        );
        // 允许用户自定义模板的字符串替换
        if(is_array($this->config['TMPL_PARSE_STRING']))
            $replace = array_merge($replace, $this->config['TMPL_PARSE_STRING']);
        $content = str_replace(array_keys($replace), array_values($replace), $content);
        return $content;
    }

    /**
     * 模板解析入口
     * 支持普通标签和TagLib解析 支持自定义标签库
     * @access public
     * @param string $content 要解析的模板内容
     * @return string
     */
    public function parse($content) {
        // 内容为空不解析
        if(empty($content)) return '';
        // 检查include语法
        $content = $this->parseInclude($content);
        // 检查PHP语法
        $content = $this->parsePhp($content);
        // 首先替换literal标签内容
        $content = preg_replace_callback('/'.$this->config['taglib_begin'].'literal'.$this->config['taglib_end'].'(.*?)'.$this->config['taglib_begin'].'\/literal'.$this->config['taglib_end'].'/is', array($this, 'parseLiteral'),$content);

        // 获取需要引入的标签库列表
        // 标签库只需要定义一次，允许引入多个一次
        // 一般放在文件的最前面
        // 格式：<taglib name="html,mytag..." />
        // 当TAGLIB_LOAD配置为true时才会进行检测
        if($this->config['TAGLIB_LOAD']) {
            $this->getIncludeTagLib($content);
            if(!empty($this->tagLib)) {
                // 对导入的TagLib进行解析
                foreach($this->tagLib as $tagLibName) {
                    $this->parseTagLib($tagLibName, $content);
                }
            }
        }

        // 预先加载的标签库 无需在每个模板中使用taglib标签加载 但必须使用标签库XML前缀
        if($this->config['TAGLIB_PRE_LOAD']) {
            $tagLibs =  explode(',',$this->config['TAGLIB_PRE_LOAD']);
            foreach($tagLibs as $tag) {
                $this->parseTagLib($tag,$content);
            }
        }

        // 内置标签库 无需使用taglib标签导入就可以使用 并且不需使用标签库XML前缀
        $tagLibs = explode(',',$this->config['TAGLIB_BUILD_IN']);
        foreach($tagLibs as $tag) {
            $this->parseTagLib($tag,$content,true);
        }

        //解析普通模板标签 {tagName}
        $content = preg_replace_callback('/('.$this->config['tmpl_begin'].')([^\d\s'.$this->config['tmpl_begin'].$this->config['tmpl_end'].'].+?)('.$this->config['tmpl_end'].')/is', array($this, 'parseTag'),$content);
        return $content;
    }

    /**
     * 检查PHP语法
     * @param $content
     * @return mixed
     * @throws \Exception
     */
    protected function parsePhp($content) {
        if(ini_get('short_open_tag')){
            // 开启短标签的情况要将<?标签用echo方式输出 否则无法正常输出xml标识
            $content = preg_replace('/(<\?(?!php|=|$))/i', '<?php echo \'\\1\'; ?>'."\n", $content );
        }
        // PHP语法检查
        if($this->config['TMPL_DENY_PHP'] && false !== strpos($content,'<?php')) {
            throw new \Exception('模板禁用PHP代码');
        }
        return $content;
    }

    /**
     * 解析模板中的include标签
     * @param $content
     * @return string
     */
    protected function parseInclude($content) {
        //读取模板中的include标签
        $find = preg_match_all('/'.$this->config['taglib_begin'].'include\s(.+?)\s*?\/'.$this->config['taglib_end'].'/is',$content,$matches);
        if($find) {
            for($i=0;$i<$find;$i++) {
                $include = $matches[1][$i];
                $array = $this->parseXmlAttr($include);
                $file = $array['file'];
                unset($array['file']);
                $content = str_replace($matches[0][$i], $this->parseIncludeItem($file, $array), $content);
            }
        }
        return $content;
    }

    /**
     * 分析XML属性
     * @access private
     * @param string $attr XML属性字符串
     * @return array
     * @throws \Exception
     */
    private function parseXmlAttr($attr) {
        $xmlStr = '<tpl><tag '.$attr.' /></tpl>';
         $xml = simplexml_load_string($xmlStr);
        if(!$xml)
            throw new \Exception('XML标签语法错误');
        $xml = (array)($xml->tag->attributes());
        return array_change_key_case($xml['@attributes']);
    }

    /**
     * 替换页面中的literal标签
     * @access private
     * @param string $content  模板内容
     * @return string|false
     */
    private function parseLiteral($content) {
        if(is_array($content)) $content = $content[1];
        if(trim($content)=='')  return '';
        //$content            =   stripslashes($content);
        $i                  =   count($this->literal);
        $parseStr           =   "<!--###literal{$i}###-->";
        $this->literal[$i]  =   $content;
        return $parseStr;
    }

    /**
     * 还原被替换的literal标签
     * @access private
     * @param string $tag  literal标签序号
     * @return string|false
     */
    private function restoreLiteral($tag) {
        if(is_array($tag)) $tag = $tag[1];
        // 还原literal标签
        $parseStr = $this->literal[$tag];
        // 销毁literal记录
        unset($this->literal[$tag]);
        return $parseStr;
    }

    /**
     * 搜索模板页面中包含的TagLib库
     * 并返回列表
     * @access public
     * @param string $content  模板内容
     * @return string|false
     */
    public function getIncludeTagLib(&$content) {
        //搜索是否有TagLib标签
        $find = preg_match('/'.$this->config['taglib_begin'].'taglib\s(.+?)(\s*?)\/'.$this->config['taglib_end'].'\W/is',$content,$matches);
        if($find) {
            //替换TagLib标签
            $content = str_replace($matches[0],'',$content);
            //解析TagLib标签
            $array = $this->parseXmlAttr($matches[1]);
            $this->tagLib = explode(',',$array['name']);
        }
        return;
    }

    /**
     * TagLib库解析
     * @access public
     * @param string $tagLib 要解析的标签库
     * @param string $content 要解析的模板内容
     * @param boolean $hide 是否隐藏标签库前缀
     * @return string
     * @throws \Exception
     */
    public function parseTagLib($tagLib, &$content, $hide = false) {
        if(strpos($tagLib,'\\')) {
            // 支持指定标签库的命名空间
            $className  =   $tagLib;
            $tagLib     =   substr($tagLib,strrpos($tagLib,'\\')+1);
        } else {
            $className  =   'Lcd\\View\\Engine\\TagLib\\'.ucwords($tagLib);
        }

        if(!isset(self::$_tLib[$className])) {
            if(class_exists($className)) {
                self::$_tLib[$className] = new $className($this);
            } else {
                throw new \Exception("$className 标签库不存在");
            }
        }

        $tLib = self::$_tLib[$className];

        if(!($tLib instanceof TagLib))
            throw new \Exception("$className 非法");

        $that = $this;
        foreach ($tLib->getTags() as $name=>$val){
            $tags = array($name);
            if(isset($val['alias'])) {// 别名设置
                $tags       = explode(',',$val['alias']);
                $tags[]     =  $name;
            }
            $level      =   isset($val['level'])?$val['level']:1;
            $closeTag   =   isset($val['close'])?$val['close']:true;
            foreach ($tags as $tag){
                $parseTag = !$hide? $tagLib.':'.$tag: $tag;// 实际要解析的标签名称
                if(!method_exists($tLib,'_'.$tag)) {
                    // 别名可以无需定义解析方法
                    $tag  =  $name;
                }
                $n1 = empty($val['attr'])?'(\s*?)':'\s([^'.$this->config['taglib_end'].']*)';
//                $this->tempVar = array($tagLib, $tag);

                if (!$closeTag){
                    $patterns       = '/'.$this->config['taglib_begin'].$parseTag.$n1.'\/(\s*?)'.$this->config['taglib_end'].'/is';
                    $content        = preg_replace_callback($patterns, function($matches) use($tLib,$tag,$that){
                        return $that->parseXmlTag($tLib,$tag,$matches[1],$matches[2]);
                    },$content);
                }else{
                    $patterns       = '/'.$this->config['taglib_begin'].$parseTag.$n1.$this->config['taglib_end'].'(.*?)'.$this->config['taglib_begin'].'\/'.$parseTag.'(\s*?)'.$this->config['taglib_end'].'/is';
                    for($i=0;$i<$level;$i++) {
                        $content=preg_replace_callback($patterns,function($matches) use($tLib,$tag,$that){
                            return $that->parseXmlTag($tLib,$tag,$matches[1],$matches[2]);
                        },$content);
                    }
                }
            }
        }
    }

    /**
     * 解析标签库的标签
     * 需要调用对应的标签库文件解析类
     * @access public
     * @param object $tagLib  标签库对象实例
     * @param string $tag  标签名
     * @param string $attr  标签属性
     * @param string $content  标签内容
     * @return string|false
     */
    public function parseXmlTag($tagLib,$tag,$attr,$content) {
        if(ini_get('magic_quotes_sybase'))
            $attr   =	str_replace('\"','\'',$attr);
        $parse      =	'_'.$tag;
        $content    =	trim($content);
        $tags		=   $tagLib->parseXmlAttr($attr,$tag);
        return $tagLib->$parse($tags,$content);
    }

    /**
     * 模板标签解析
     * 格式： {TagName:args [|content] }
     * @access public
     * @param string $tagStr 标签内容
     * @return string
     */
    public function parseTag($tagStr){
        if(is_array($tagStr)) $tagStr = $tagStr[2];
        //if (MAGIC_QUOTES_GPC) {
        $tagStr = stripslashes($tagStr);
        //}
        //还原非模板标签
        if(preg_match('/^[\s|\d]/is',$tagStr))
            //过滤空格和数字打头的标签
            return $this->config['TMPL_L_DELIM'] . $tagStr .$this->config['TMPL_R_DELIM'];
        $flag   =  substr($tagStr,0,1);
        $flag2  =  substr($tagStr,1,1);
        $name   = substr($tagStr,1);
        if('$' == $flag && '.' != $flag2 && '(' != $flag2){ //解析模板变量 格式 {$varName}
            return $this->parseVar($name);
        }elseif('-' == $flag || '+'== $flag){ // 输出计算
            return  '<?php echo '.$flag.$name.';?>';
        }elseif(':' == $flag){ // 输出某个函数的结果
            return  '<?php echo '.$name.';?>';
        }elseif('~' == $flag){ // 执行某个函数
            return  '<?php '.$name.';?>';
        }elseif(substr($tagStr,0,2)=='//' || (substr($tagStr,0,2)=='/*' && substr(rtrim($tagStr),-2)=='*/')){
            //注释标签
            return '';
        }
        // 未识别的标签直接返回
        return $this->config['TMPL_L_DELIM'] . $tagStr .$this->config['TMPL_R_DELIM'];
    }

    /**
     * 模板变量解析,支持使用函数
     * 格式： {$varName|function1|function2=arg1,arg2}
     * @access public
     * @param string $varStr 变量数据
     * @return string
     */
    public function parseVar($varStr){
        $varStr = trim($varStr);
        static $_varParseList = array();
        //如果已经解析过该变量字串，则直接返回变量值
        if(isset($_varParseList[$varStr])) return $_varParseList[$varStr];
        $parseStr = '';
//        $varExists  =   true;
        if(!empty($varStr)) {
            $varArray = explode('|',$varStr);
            //取得变量名称
            $var = array_shift($varArray);
            if('Lcd.' == substr($var,0,7)){
                // 所有以Lcd.打头的以特殊变量对待 无需模板赋值就可以输出
                $name = $this->parseLcdVar($var);
            }elseif( false !== strpos($var,'.')) {
                //支持 {$var.property}
                $vars = explode('.',$var);
                $var  =  array_shift($vars);
                switch(strtolower($this->config['TMPL_VAR_IDENTIFY'])) {
                    case 'array': // 识别为数组
                        $name = '$'.$var;
                        foreach ($vars as $val)
                            $name .= '["'.$val.'"]';
                        break;
                    case 'obj':  // 识别为对象
                        $name = '$'.$var;
                        foreach ($vars as $val)
                            $name .= '->'.$val;
                        break;
                    default:  // 自动判断数组或对象 只支持二维
                        $name = 'is_array($'.$var.')?$'.$var.'["'.$vars[0].'"]:$'.$var.'->'.$vars[0];
                }
            }elseif(false !== strpos($var,'[')) {
                //支持 {$var['key']} 方式输出数组
                $name = "$".$var;
                preg_match('/(.+?)\[(.+?)\]/is',$var,$match);
//                $var = $match[1];
            }elseif(false !==strpos($var,':') && false ===strpos($var,'(') && false ===strpos($var,'::') && false ===strpos($var,'?')){
                //支持 {$var:property} 方式输出对象的属性
//                $vars = explode(':',$var);
                $var  =  str_replace(':','->',$var);
                $name = "$".$var;
//                $var  = $vars[0];
            }else {
                $name = "$$var";
            }
            //对变量使用函数
            if(count($varArray)>0)
                $name = $this->parseVarFunction($name,$varArray);
            $parseStr = '<?php echo ('.$name.'); ?>';
        }
        $_varParseList[$varStr] = $parseStr;
        return $parseStr;
    }

    /**
     * 对模板变量使用函数
     * 格式 {$varName|function1|function2=arg1,arg2}
     * @access public
     * @param string $name 变量名
     * @param array $varArray  函数列表
     * @return string
     */
    public function parseVarFunction($name,$varArray){
        //对变量使用函数
        $length = count($varArray);
        //取得模板禁止使用函数列表
        $template_deny_funs = explode(',',$this->config['TMPL_DENY_FUNC_LIST']);
        for($i=0;$i<$length ;$i++ ){
            $args = explode('=',$varArray[$i],2);
            //模板函数过滤
            $fun = strtolower(trim($args[0]));
            switch($fun) {
                case 'default':  // 特殊模板函数
                    $name = '(isset('.$name.') && ('.$name.' !== ""))?('.$name.'):'.$args[1];
                    break;
                default:  // 通用模板函数
                    if(!in_array($fun,$template_deny_funs)){
                        if(isset($args[1])){
                            if(strstr($args[1],'###')){
                                $args[1] = str_replace('###',$name,$args[1]);
                                $name = "$fun($args[1])";
                            }else{
                                $name = "$fun($name,$args[1])";
                            }
                        }else if(!empty($args[0])){
                            $name = "$fun($name)";
                        }
                    }
            }
        }
        return $name;
    }

    /**
     * 特殊模板变量解析
     * 格式 以 $Think. 打头的变量属于特殊模板变量
     * @access public
     * @param string $varStr  变量字符串
     * @return string
     */
    public function parseLcdVar($varStr) {
        $vars = explode('.',$varStr);
        $vars[1] = strtoupper(trim($vars[1]));
        $parseStr = '';
        if(count($vars)>=3){
            $vars[2] = trim($vars[2]);
            switch($vars[1]){
                case 'SERVER':
                    $parseStr = '$_SERVER[\''.strtoupper($vars[2]).'\']';break;
                case 'GET':
                    $parseStr = '$_GET[\''.$vars[2].'\']';break;
                case 'POST':
                    $parseStr = '$_POST[\''.$vars[2].'\']';break;
                case 'COOKIE':
                    if(isset($vars[3])) {
                        $parseStr = '$_COOKIE[\''.$vars[2].'\'][\''.$vars[3].'\']';
                    }else{
                        $parseStr = 'cookie(\''.$vars[2].'\')';
                    }
                    break;
                case 'SESSION':
                    if(isset($vars[3])) {
                        $parseStr = '$_SESSION[\''.$vars[2].'\'][\''.$vars[3].'\']';
                    }else{
                        $parseStr = 'session(\''.$vars[2].'\')';
                    }
                    break;
                case 'ENV':
                    $parseStr = '$_ENV[\''.strtoupper($vars[2]).'\']';break;
                case 'REQUEST':
                    $parseStr = '$_REQUEST[\''.$vars[2].'\']';break;
                case 'CONST':
                    $parseStr = strtoupper($vars[2]);break;
                case 'LANG':
                    $parseStr = 'Lang::read("'.$vars[2].'")';break;
                case 'CONFIG':
                    if(isset($vars[3])) {
                        $vars[2] .= '.'.$vars[3];
                    }
                    $parseStr = 'Config::read("'.$vars[2].'")';break;
                default:break;
            }
        }else if(count($vars)==2){
            switch($vars[1]){
                case 'NOW':
                    $parseStr = "date('Y-m-d g:i a',time())";
                    break;
                case 'VERSION':
                    $parseStr = 'LCD_VERSION';
                    break;
                default:
                    if(defined($vars[1]))
                        $parseStr = $vars[1];
            }
        }
        return $parseStr;
    }

    /**
     * 加载公共模板并缓存 和当前模板在同一路径，否则使用相对路径
     * @access private
     * @param string $tmpPublicName  公共模板文件名
     * @param array $vars  要传递的变量列表
     * @return string
     */
    private function parseIncludeItem($tmpPublicName, $vars=array()){
        // 分析模板文件名并读取内容
        $parseStr = $this->parseTemplateName($tmpPublicName);
        // 替换变量
        foreach ($vars as $key=>$val) {
            $parseStr = str_replace('['.$key.']',$val,$parseStr);
        }
        // 再次对包含文件进行模板分析
        return $this->parseInclude($parseStr);
    }

    /**
     * 分析加载的模板文件并读取内容 支持多个模板文件读取
     * @access private
     * @param string $templateName  模板文件名
     * @return string
     */
    private function parseTemplateName($templateName){
        $array = explode(',',$templateName);
        $parseStr = '';
        foreach ($array as $templateName) {
            if(empty($templateName)) continue;
            if(false === strpos($templateName, '.php')) {
                //解析规则为 模块/控制器/操作
                $templateName = $this->_getTemplate($templateName);
            }

            if(!is_file($templateName)) {
                throw new \Exception("$templateName 模板不存在");
            }
            // 获取模板文件内容
            $parseStr .= file_get_contents($templateName);
        }
        return $parseStr;
    }

    /**
     * 获取模板文件路径
     * @param $name
     * @return string
     * @throws \Exception
     */
    private function _getTemplate($name) {
        $name = trim($name, '/');

        //模板名称
        if(empty($name)) {
            $name = Request::$action;
        }

        if(strpos($name, '/')!==false) {

            $arr = explode('/', $name);

            $action = array_pop($arr);
            $controller = array_pop($arr);
            $module = !empty($arr) ? array_pop($arr) : Request::$module;

            if(!empty($arr)) {
                throw new \Exception('模板参数错误');
            }
        } else {
            $action = $name;
            $controller = Request::$controller;
            $module = Request::$module;
        }

        return MODULE_PATH . $module . DS . 'Template'. DS  . Request::$theme . DS . $controller . DS . ucfirst($action) . '.php';
    }
}