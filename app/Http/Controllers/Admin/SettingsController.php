<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * The settings service instance.
     *
     * @var \App\Services\SettingsService
     */
    protected $settingsService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\SettingsService  $settingsService
     * @return void
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Display the settings page.
     *
     * @param  string  $group
     * @return \Illuminate\Http\Response
     */
    public function index($group = 'general')
    {
        $groups = Setting::getGroups();
        
        if (!array_key_exists($group, $groups)) {
            $group = 'general';
        }

        $settings = Setting::getByGroup($group);
        
        return view('admin.settings.index', [
            'settings' => $settings,
            'groups' => $groups,
            'currentGroup' => $group,
        ]);
    }

    /**
     * Update the settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $group)
    {
        $settings = Setting::getByGroup($group);
        $rules = [];
        $messages = [];

        // Build validation rules
        foreach ($settings as $setting) {
            if ($setting->is_system) {
                continue;
            }

            $key = "settings.{$setting->key}";
            
            switch ($setting->type) {
                case 'integer':
                    $rules[$key] = 'nullable|integer';
                    break;
                case 'float':
                    $rules[$key] = 'nullable|numeric';
                    break;
                case 'boolean':
                    $rules[$key] = 'nullable|boolean';
                    break;
                case 'array':
                case 'json':
                case 'object':
                    $rules[$key] = 'nullable|json';
                    break;
                case 'select':
                    $options = $setting->options ? array_keys($setting->options) : [];
                    $rules[$key] = 'nullable|in:' . implode(',', $options);
                    break;
                case 'multiselect':
                    $options = $setting->options ? array_keys($setting->options) : [];
                    $rules[$key] = 'nullable|array';
                    $rules["{$key}.*"] = 'in:' . implode(',', $options);
                    break;
                case 'file':
                case 'image':
                    $rules[$key] = 'nullable|file';
                    if ($setting->type === 'image') {
                        $rules[$key] .= '|image';
                    }
                    break;
                default:
                    $rules[$key] = 'nullable|string';
                    break;
            }

            $messages["{$key}.required"] = "The {$setting->label} field is required.";
            $messages["{$key}.integer"] = "The {$setting->label} must be an integer.";
            $messages["{$key}.numeric"] = "The {$setting->label} must be a number.";
            $messages["{$key}.boolean"] = "The {$setting->label} must be a boolean.";
            $messages["{$key}.json"] = "The {$setting->label} must be a valid JSON.";
            $messages["{$key}.in"] = "The selected {$setting->label} is invalid.";
            $messages["{$key}.array"] = "The {$setting->label} must be an array.";
            $messages["{$key}.*.in"] = "The selected {$setting->label} is invalid.";
            $messages["{$key}.file"] = "The {$setting->label} must be a file.";
            $messages["{$key}.image"] = "The {$setting->label} must be an image.";
        }

        // Validate the request
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update settings
        foreach ($settings as $setting) {
            if ($setting->is_system) {
                continue;
            }

            $key = "settings.{$setting->key}";
            $value = $request->input($key);

            if ($setting->type === 'file' || $setting->type === 'image') {
                if ($request->hasFile($key)) {
                    $file = $request->file($key);
                    $path = $file->store('settings', 'public');
                    
                    // Delete old file if exists
                    if ($setting->value) {
                        Storage::disk('public')->delete($setting->value);
                    }
                    
                    $value = $path;
                } else {
                    continue;
                }
            }

            if ($setting->type === 'boolean') {
                $value = (bool) $value;
            }

            if ($setting->type === 'multiselect' && is_array($value)) {
                $value = json_encode($value);
            }

            $setting->value = $value;
            $setting->save();
        }

        // Clear cache
        Cache::forget('settings');

        // Log activity
        auth()->user()->logActivity(
            'update',
            'settings',
            "Updated {$group} settings",
            ['group' => $group]
        );

        return redirect()->route('admin.settings.index', $group)
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Show the create setting form.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $groups = Setting::getGroups();
        $types = Setting::getTypes();

        return view('admin.settings.create', [
            'groups' => $groups,
            'types' => $types,
        ]);
    }

    /**
     * Store a new setting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:settings',
            'label' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(Setting::getTypes())),
            'group' => 'required|string|in:' . implode(',', array_keys(Setting::getGroups())),
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'order' => 'integer',
            'options' => 'nullable|json',
        ]);

        $setting = Setting::create([
            'key' => $request->key,
            'label' => $request->label,
            'type' => $request->type,
            'group' => $request->group,
            'description' => $request->description,
            'is_public' => $request->is_public ?? false,
            'is_system' => false,
            'order' => $request->order ?? 0,
            'options' => $request->options ? json_decode($request->options, true) : null,
        ]);

        // Log activity
        auth()->user()->logActivity(
            'create',
            'settings',
            "Created setting: {$setting->key}",
            ['setting_id' => $setting->id]
        );

        return redirect()->route('admin.settings.index', $setting->group)
            ->with('success', 'Setting created successfully.');
    }

    /**
     * Show the edit setting form.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $setting = Setting::findOrFail($id);
        $groups = Setting::getGroups();
        $types = Setting::getTypes();

        return view('admin.settings.edit', [
            'setting' => $setting,
            'groups' => $groups,
            'types' => $types,
        ]);
    }

    /**
     * Update the setting.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSetting(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);

        $request->validate([
            'key' => 'required|string|max:255|unique:settings,key,' . $setting->id,
            'label' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(Setting::getTypes())),
            'group' => 'required|string|in:' . implode(',', array_keys(Setting::getGroups())),
            'description' => 'nullable|string',
            'is_public' => 'boolean',
            'order' => 'integer',
            'options' => 'nullable|json',
        ]);

        $setting->update([
            'key' => $request->key,
            'label' => $request->label,
            'type' => $request->type,
            'group' => $request->group,
            'description' => $request->description,
            'is_public' => $request->is_public ?? false,
            'order' => $request->order ?? 0,
            'options' => $request->options ? json_decode($request->options, true) : null,
        ]);

        // Log activity
        auth()->user()->logActivity(
            'update',
            'settings',
            "Updated setting: {$setting->key}",
            ['setting_id' => $setting->id]
        );

        return redirect()->route('admin.settings.index', $setting->group)
            ->with('success', 'Setting updated successfully.');
    }

    /**
     * Delete the setting.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $setting = Setting::findOrFail($id);

        // Prevent deletion of system settings
        if ($setting->is_system) {
            return redirect()->route('admin.settings.index', $setting->group)
                ->with('error', 'System settings cannot be deleted.');
        }

        $group = $setting->group;
        $key = $setting->key;

        // Delete file if exists
        if (($setting->type === 'file' || $setting->type === 'image') && $setting->value) {
            Storage::disk('public')->delete($setting->value);
        }

        $setting->delete();

        // Log activity
        auth()->user()->logActivity(
            'delete',
            'settings',
            "Deleted setting: {$key}",
            ['key' => $key]
        );

        return redirect()->route('admin.settings.index', $group)
            ->with('success', 'Setting deleted successfully.');
    }

    /**
     * Show the backup settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function backup()
    {
        $backups = $this->settingsService->getBackups();

        return view('admin.settings.backup', [
            'backups' => $backups,
        ]);
    }

    /**
     * Create a new backup.
     *
     * @return \Illuminate\Http\Response
     */
    public function createBackup()
    {
        try {
            Artisan::call('backup:run');
            $output = Artisan::output();

            // Log activity
            auth()->user()->logActivity(
                'create',
                'backups',
                'Created a new backup',
                ['output' => $output]
            );

            return redirect()->route('admin.settings.backup')
                ->with('success', 'Backup created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.backup')
                ->with('error', 'Failed to create backup: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup.
     *
     * @param  string  $filename
     * @return \Illuminate\Http\Response
     */
    public function downloadBackup($filename)
    {
        $backupPath = $this->settingsService->getBackupPath($filename);

        if (!file_exists($backupPath)) {
            return redirect()->route('admin.settings.backup')
                ->with('error', 'Backup file not found.');
        }

        // Log activity
        auth()->user()->logActivity(
            'download',
            'backups',
            "Downloaded backup: {$filename}",
            ['filename' => $filename]
        );

        return response()->download($backupPath);
    }

    /**
     * Delete a backup.
     *
     * @param  string  $filename
     * @return \Illuminate\Http\Response
     */
    public function deleteBackup($filename)
    {
        $result = $this->settingsService->deleteBackup($filename);

        if ($result) {
            // Log activity
            auth()->user()->logActivity(
                'delete',
                'backups',
                "Deleted backup: {$filename}",
                ['filename' => $filename]
            );

            return redirect()->route('admin.settings.backup')
                ->with('success', 'Backup deleted successfully.');
        }

        return redirect()->route('admin.settings.backup')
            ->with('error', 'Failed to delete backup.');
    }

    /**
     * Show the system information page.
     *
     * @return \Illuminate\Http\Response
     */
    public function systemInfo()
    {
        $systemInfo = $this->settingsService->getSystemInfo();

        return view('admin.settings.system_info', [
            'systemInfo' => $systemInfo,
        ]);
    }

    /**
     * Clear application cache.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');

            // Log activity
            auth()->user()->logActivity(
                'clear',
                'cache',
                'Cleared application cache',
                []
            );

            return redirect()->route('admin.settings.system_info')
                ->with('success', 'Application cache cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.system_info')
                ->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Show the email settings test page.
     *
     * @return \Illuminate\Http\Response
     */
    public function emailTest()
    {
        return view('admin.settings.email_test');
    }

    /**
     * Send a test email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $result = $this->settingsService->sendTestEmail($request->email);

            if ($result) {
                // Log activity
                auth()->user()->logActivity(
                    'send',
                    'email',
                    'Sent test email',
                    ['email' => $request->email]
                );

                return redirect()->route('admin.settings.email_test')
                    ->with('success', 'Test email sent successfully.');
            }

            return redirect()->route('admin.settings.email_test')
                ->with('error', 'Failed to send test email.');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.email_test')
                ->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    /**
     * Show the theme settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function theme()
    {
        $themes = $this->settingsService->getAvailableThemes();
        $currentTheme = Setting::getValue('app_theme', 'default');

        return view('admin.settings.theme', [
            'themes' => $themes,
            'currentTheme' => $currentTheme,
        ]);
    }

    /**
     * Update the theme settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateTheme(Request $request)
    {
        $request->validate([
            'theme' => 'required|string',
            'primary_color' => 'required|string',
            'secondary_color' => 'required|string',
            'custom_css' => 'nullable|string',
        ]);

        Setting::setValue('app_theme', $request->theme);
        Setting::setValue('primary_color', $request->primary_color);
        Setting::setValue('secondary_color', $request->secondary_color);
        Setting::setValue('custom_css', $request->custom_css);

        // Generate CSS file
        $this->settingsService->generateThemeCSS(
            $request->primary_color,
            $request->secondary_color,
            $request->custom_css
        );

        // Log activity
        auth()->user()->logActivity(
            'update',
            'theme',
            'Updated theme settings',
            [
                'theme' => $request->theme,
                'primary_color' => $request->primary_color,
                'secondary_color' => $request->secondary_color,
            ]
        );

        return redirect()->route('admin.settings.theme')
            ->with('success', 'Theme settings updated successfully.');
    }
}

