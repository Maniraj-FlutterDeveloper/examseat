<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'is_public',
        'is_system',
        'order',
        'options',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
        'is_system' => 'boolean',
        'options' => 'json',
    ];

    /**
     * Get the setting value with proper type casting.
     *
     * @return mixed
     */
    public function getTypedValueAttribute()
    {
        if ($this->value === null) {
            return null;
        }

        switch ($this->type) {
            case 'boolean':
                return (bool) $this->value;
            case 'integer':
                return (int) $this->value;
            case 'float':
                return (float) $this->value;
            case 'array':
            case 'json':
                return json_decode($this->value, true);
            case 'object':
                return json_decode($this->value);
            default:
                return $this->value;
        }
    }

    /**
     * Set the setting value with proper type casting.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setTypedValueAttribute($value)
    {
        if ($value === null) {
            $this->attributes['value'] = null;
            return;
        }

        switch ($this->type) {
            case 'array':
            case 'json':
            case 'object':
                $this->attributes['value'] = json_encode($value);
                break;
            default:
                $this->attributes['value'] = (string) $value;
                break;
        }
    }

    /**
     * Get all available setting groups.
     *
     * @return array
     */
    public static function getGroups()
    {
        return [
            'general' => 'General',
            'appearance' => 'Appearance',
            'email' => 'Email',
            'security' => 'Security',
            'notifications' => 'Notifications',
            'seating_plans' => 'Seating Plans',
            'question_papers' => 'Question Papers',
            'backup' => 'Backup',
            'system' => 'System',
        ];
    }

    /**
     * Get all available setting types.
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            'string' => 'String',
            'text' => 'Text',
            'integer' => 'Integer',
            'float' => 'Float',
            'boolean' => 'Boolean',
            'array' => 'Array',
            'json' => 'JSON',
            'object' => 'Object',
            'select' => 'Select',
            'multiselect' => 'Multi-select',
            'color' => 'Color',
            'date' => 'Date',
            'time' => 'Time',
            'datetime' => 'Date & Time',
            'file' => 'File',
            'image' => 'Image',
        ];
    }

    /**
     * Get a setting value by key.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return $setting->typed_value ?? $default;
    }

    /**
     * Set a setting value by key.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  string|null  $type
     * @return \App\Models\Setting
     */
    public static function setValue($key, $value, $type = null)
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return null;
        }

        if ($type) {
            $setting->type = $type;
        }

        $setting->typed_value = $value;
        $setting->save();

        return $setting;
    }

    /**
     * Get all settings by group.
     *
     * @param  string  $group
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByGroup($group)
    {
        return self::where('group', $group)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get all public settings.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPublic()
    {
        return self::where('is_public', true)
            ->orderBy('group')
            ->orderBy('order')
            ->get();
    }
}

