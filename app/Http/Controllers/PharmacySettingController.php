<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PharmacySettingController extends Controller
{
    public function index()
    {
        return view('settings.pharmacy', [
            'appName' => env('APP_NAME', 'Pharmacy Inventory'),
            'mailFromAddress' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
            'mailFromName' => env('MAIL_FROM_NAME', env('APP_NAME', 'Pharmacy Inventory')),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:100'],
            'mail_from_name' => ['required', 'string', 'max:100'],
            'mail_from_address' => ['required', 'email', 'max:255'],
        ]);

        $this->updateEnv('APP_NAME', $validated['app_name']);
        $this->updateEnv('MAIL_FROM_NAME', $validated['mail_from_name']);
        $this->updateEnv('MAIL_FROM_ADDRESS', $validated['mail_from_address']);

        return redirect()->route('pharmacy.settings')->with('success', 'Pharmacy information updated successfully.');
    }

    private function updateEnv(string $key, string $value): void
    {
        $path = base_path('.env');
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        $updated = false;

        foreach ($lines as $index => $line) {
            if (str_starts_with(trim($line), $key . '=')) {
                $lines[$index] = $key . '="' . str_replace('"', '\\"', $value) . '"';
                $updated = true;
                break;
            }
        }

        if (! $updated) {
            $lines[] = $key . '="' . str_replace('"', '\\"', $value) . '"';
        }

        file_put_contents($path, implode(PHP_EOL, $lines) . PHP_EOL);
    }
}
