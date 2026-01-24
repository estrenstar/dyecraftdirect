<?php
    //Some shops has HTTP_CATALOG defined in catalog config.php.... so we used second condition about "config_theme" config variable
    if($this->config->get('config_theme')) {
        if (version_compare(VERSION, '2.1.0.3', '>')) {
            $this->registry->set('GMTMaster', new google_marketing_tools\master($this->registry));
            $this->registry->set('GoogleTagManager', new google_marketing_tools\google_tag_manager($this->registry));
            $this->registry->set('DataLayer', new google_marketing_tools\data_layer($this->registry));
            $this->registry->set('FacebookApiConversions', new google_marketing_tools\facebook_api_conversions($this->registry));
            $this->registry->set('TwoPerformance', new google_marketing_tools\twoperformant($this->registry));

        } elseif (version_compare(VERSION, '2', '>=')) {
            $registry->set('GMTMaster', new google_marketing_tools\master($registry));
            $registry->set('GoogleTagManager', new google_marketing_tools\google_tag_manager($registry));
            $registry->set('DataLayer', new google_marketing_tools\data_layer($registry));
            $registry->set('FacebookApiConversions', new google_marketing_tools\facebook_api_conversions($registry));
            $registry->set('TwoPerformance', new google_marketing_tools\twoperformant($registry));

        } else {
            require_once(VQMod::modCheck(DIR_SYSTEM . 'library/user.php'));
            $registry->set('user', new User($registry));

            include_once(DIR_SYSTEM . 'library/google_marketing_tools/master.php');
            $registry->set('GMTMaster', new google_marketing_tools\master($registry));

            include_once(DIR_SYSTEM . 'library/google_marketing_tools/google_tag_manager.php');
            $registry->set('GoogleTagManager', new google_marketing_tools\google_tag_manager($registry));

            include_once(DIR_SYSTEM . 'library/google_marketing_tools/data_layer.php');
            $registry->set('DataLayer', new google_marketing_tools\data_layer($registry));

            include_once(DIR_SYSTEM . 'library/google_marketing_tools/facebook_api_conversions.php');
            $registry->set('FacebookApiConversions', new google_marketing_tools\facebook_api_conversions($registry));

            include_once(DIR_SYSTEM . 'library/google_marketing_tools/twoperformant.php');
            $registry->set('TwoPerformance', new google_marketing_tools\twoperformant($registry));
        }
    }
?>