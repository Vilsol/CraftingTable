<?php

class ServerType extends PluginHolder {

    protected $connection = 'mongodb';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'servertypes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    public static function boot() {
        parent::boot();

        ServerType::deleting(function($servertype) {
            foreach(Network::all() as $network) {
                foreach ($network->servertypes()->where('server_type_id', '=', $servertype->id)->get() as $networkServerType) {
                    $networkServerType->delete();
                }
                foreach($network->servers()->get()->all() as $server) {
                    $server->delete();
                }
            }

            return true;
        });
    }

    /**
     * Worlds
     *
     * @return object
     */
    public function worlds()
    {
        return $this->embedsMany('ServerTypeWorld');
    }

    /**
     * Default world
     *
     * @return object
     */
    public function defaultWorld() {
        return $this->worlds()->where('defaultWorld', '=', '1')->first();
    }


}