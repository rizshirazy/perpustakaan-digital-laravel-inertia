<?php

if (! function_exists('flashMessage')) {

    /**
     * Flash a message to the session.
     *
     * @param string $message The message to flash.
     * @param string $type The type of message (e.g., 'success', 'error').
     */
    function flashMessage($message, $type = 'success'): void
    {
        session()->flash('message', $message);
        session()->flash('type', $type);
    }
}

if (! function_exists('usernameGenerator')) {

    /**
     * Generate a username based on the given name.
     *
     * @param string $name The name to generate the username from.
     * @return string The generated username.
     */
    function usernameGenerator($name): string
    {
        $baseUsername = strtolower(preg_replace('/\s+/', '.', trim($name)));
        $username = $baseUsername;
        $counter = 1;

        while (\App\Models\User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }
}


if (! function_exists('signatureMidtrans')) {

    /**
     * Generate a Midtrans signature.
     * @param string $orderId The order ID.
     * @param string $statusCode The status code.
     * @param string $amount The amount.
     * @param string $serverKey The server key.
     * @return string The generated signature.
     */
    function signatureMidtrans($orderId, $statusCode, $amount, $serverKey): string
    {
        $input = $orderId . $statusCode . $amount . $serverKey;
        return hash('sha512', $input);
    }
}
