<?php
/**
 * Created by PhpStorm.
 * User: Savaryn Yaroslav
 * Date: 22.05.2016
 * Time: 12:02
 */

namespace yariksav\actives\controls;

use yii;

class SocialAuth extends CollectionControl {
    /**
     * @var string name of the auth client collection application component.
     * This component will be used to fetch services value if it is not set.
     */
    public $clientCollection = 'authClientCollection';
    /**
     * @var string URL route configuration
     */
    public $url;
    /**
     * @var int default popup width
     */
    public $popupWidth;
    /**
     * @var int default popup height
     */
    public $popupHeight;
    /**
     * @inheritdoc
     */
    public function getCollection() {
        $collection = Yii::$app->get($this->clientCollection);
        $clients = $collection->getClients();
        $services = [];
        foreach ($clients as $client) {
            $url = $this->url;
            $services[] = array_merge($client->getViewOptions(), [
                'name' => $client->getName(),
                'title' => $client->getTitle(),
                'url' => yii\helpers\Url::to([$url, 'authclient'=>$client->getId()])
            ]);
        }
        return $services;
    }

    /**
     * @inheritdoc
     */
    public function build() {
        $control = parent::build();
        if ($this->popupWidth) {
            $control['popupWidth'] = $this->popupWidth;
        }
        if ($this->popupHeight) {
            $control['popupHeight'] = $this->popupHeight;
        }
        return $control;
    }

}