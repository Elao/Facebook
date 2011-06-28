<?php

namespace Facebook\Subscriptions;

class UserEvent extends Event {

    private $entries;

    public function __construct(array $data) {
        if (!isset($data['object']) || $data['object'] != "user" || !isset($data['entry'])) {
            throw new Exception("Invalid data for Subscriptions UserEvent");
        }
        $this->setObject('user');
        
        $entries = $data["entry"];

        foreach ($entries as $entry) {
            $this->entries [] = array (
                'uid'    => $entry["uid"],
                'fields' => $entry["changed_fields"],
                'time'   => $entry["time"]
            );
        }
    }

    public function getEntries() {
        return $this->entries;
    }

}