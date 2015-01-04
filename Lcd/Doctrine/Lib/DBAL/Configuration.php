<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\DBAL;

use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\Cache\Cache;

/**
 * Configuration container for the Doctrine DBAL.
 * Doctrine DBAL的配置容器
 *
 * @since    2.0
 * @author   Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author   Jonathan Wage <jonwage@gmail.com>
 * @author   Roman Borschel <roman@code-factory.org>
 * @internal When adding a new configuration option just write a getter/setter
 *           pair and add the option to the _attributes array with a proper default value.
 */
class Configuration
{
    /**
     * The attributes that are contained in the configuration.
     * 配置容器中的所有属性
     * Values are default values.
     *
     * @var array
     */
    protected $_attributes = array();

    /**
     * Sets the SQL logger to use. Defaults to NULL which means SQL logging is disabled.
     * 设置SQL记录器并启用。默认为NULL，也意味着SQL记录器无法使用
     *
     * @param \Doctrine\DBAL\Logging\SQLLogger|null $logger
     *
     * @return void
     */
    public function setSQLLogger(SQLLogger $logger = null)
    {
        $this->_attributes['sqlLogger'] = $logger;
    }

    /**
     * Gets the SQL logger that is used.
     * 获取使用的SQL记录器
     *
     * @return \Doctrine\DBAL\Logging\SQLLogger|null
     */
    public function getSQLLogger()
    {
        return isset($this->_attributes['sqlLogger']) ?
                $this->_attributes['sqlLogger'] : null;
    }

    /**
     * Gets the cache driver implementation that is used for query result caching.
     * 获取用于查询语句缓存的缓存驱动类实现
     *
     * @return \Doctrine\Cache\Cache|null
     */
    public function getResultCacheImpl()
    {
        return isset($this->_attributes['resultCacheImpl']) ?
                $this->_attributes['resultCacheImpl'] : null;
    }

    /**
     * Sets the cache driver implementation that is used for query result caching.
     * 设置用于查询结果缓存的缓存驱动实现
     *
     * @param \Doctrine\Cache\Cache $cacheImpl
     *
     * @return void
     */
    public function setResultCacheImpl(Cache $cacheImpl)
    {
        $this->_attributes['resultCacheImpl'] = $cacheImpl;
    }

    /**
     * Sets the filter schema assets expression.
     * 设置过滤器的资源表达式
     *
     * Only include tables/sequences matching the filter expression regexp in
     * schema instances generated for the active connection when calling
     * {AbstractSchemaManager#createSchema()}.
     *
     * @param string $filterExpression 过滤的正则表达式
     *
     * @return void
     */
    public function setFilterSchemaAssetsExpression($filterExpression)
    {
        $this->_attributes['filterSchemaAssetsExpression'] = $filterExpression;
    }

    /**
     * Returns filter schema assets expression.
     *
     * @return string|null
     */
    public function getFilterSchemaAssetsExpression()
    {
        if (isset($this->_attributes['filterSchemaAssetsExpression'])) {
            return $this->_attributes['filterSchemaAssetsExpression'];
        }

        return null;
    }

    /**
     * Sets the default auto-commit mode for connections.
     * 设置自动提交
     *
     * If a connection is in auto-commit mode, then all its SQL statements will be executed and committed as individual
     * transactions. Otherwise, its SQL statements are grouped into transactions that are terminated by a call to either
     * the method commit or the method rollback. By default, new connections are in auto-commit mode.
     *
     * @param boolean $autoCommit True to enable auto-commit mode; false to disable it.
     *
     * @see   getAutoCommit
     */
    public function setAutoCommit($autoCommit)
    {
        $this->_attributes['autoCommit'] = (boolean) $autoCommit;
    }

    /**
     * Returns the default auto-commit mode for connections.
     * 获取是否自动提交
     *
     * @return boolean True if auto-commit mode is enabled by default for connections, false otherwise.
     *
     * @see    setAutoCommit
     */
    public function getAutoCommit()
    {
        if (isset($this->_attributes['autoCommit'])) {
            return $this->_attributes['autoCommit'];
        }

        return true;
    }
}
