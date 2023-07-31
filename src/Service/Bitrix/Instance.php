<?php

namespace App\Service\Bitrix;

class Instance
{
    public function install() :void
    {
        if(isset($_REQUEST[ 'event' ])){
            $result = CRest::installApp();
            if($result['rest_only'] === false):?>
                <head>
                    <script src="//api.bitrix24.com/api/v1/"></script>
                    <?php if($result['install'] == true):?>
                        <script>
                            BX24.init(function(){
                                BX24.installFinish();
                            });
                        </script>
                    <?php endif;?>
                </head>
                <body>
                <?php if($result['install'] == true):?>
                    installation has been finished
                <?php else:?>
                    installation error
                <?php endif;?>
                </body>
            <?php endif;
        }
    }
}