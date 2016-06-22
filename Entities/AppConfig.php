<?php namespace Modules\Izcore\Entities;

use Illuminate\Database\Eloquent\Model;

class AppConfig extends Model {

    protected $table    = 'izcore_app_config';
    protected $fillable = ['name', 'value'];

    /**
     * Retrieve config by name
     * @param $name
     * @return Model|null|static
     */
    public function getByName($name) {
        try {
            return $this->query()->where('name', $name)->firstOrFail();
        }
        catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Update config by name. If not existed will create new config.
     * @param $name
     * @param $value
     * @return bool
     */
    public function updateConfig($name, $value) {
        $config = $this->query()->firstOrNew(['name' => $name]);
        $config->value = $value;

        return $config->save();
    }
}