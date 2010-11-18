<?php

class PropelDatabaseToolbarExtension
{
    static $count = 0;
    static $time = 0;

    public function startSection($section_id)
    {
        $db_count = self::$count;
        $db_time = self::$time;
        
        PhpDebugToolbar::setValue('start_database_count', $db_count);
        PhpDebugToolbar::setValue('start_database_time', $db_time);
    }
    
    public function finishSection($section_id)
    {
        $db_time = 0;
        
        if (PhpDebugToolbar::isBootstrap())
        {
            $config = Propel::getConfiguration(PropelConfiguration::TYPE_ARRAY);
            
            foreach ($config['datasources'] as &$datasource)
            {
                if (is_array($datasource))
                {
                    $datasource['connection']['classname'] = 'PhpDebugToolbarPropelConnection';
                }
            }
            
            Propel::setConfiguration($config);
            
            require_once(dirname(__FILE__) . '/../lib/agavi/PhpDebugToolbarPropelConnection.class.php');
        }
        else
        {
            $db_time = self::$time;
        }
        
        PhpDebugToolbar::setValue('end_database_count', self::$count = $this->getQueryCount());
        PhpDebugToolbar::setValue('end_database_time', $db_time);
    }
    
    private function getQueryCount()
    {
        $db_manager = AgaviContext::getInstance()->getDatabaseManager();
        
        if (!empty($db_manager))
        {
            $db_connection = $db_manager->getDatabase()->getConnection();
            
            if (is_callable(array($db_connection, 'getQueryCount')))
            {
                return $db_connection->getQueryCount();
            }
        }
        
        return 0;
    }
}
