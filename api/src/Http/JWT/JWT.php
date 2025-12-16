<?php

namespace App\Http\JWT;

use App\Repositories\Entities\Users\TokenRepository;

class JWT
{
    public static function generateToken(array $payload, int $expiration): string
    {
        $header = self::base64Url_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload['exp'] = time() + $expiration;
        $payloadEncoded = self::base64Url_encode(json_encode($payload));
        $token = self::signature($header, $payloadEncoded, $_ENV['JWT_SECRET']);

        $tokenRepository = TokenRepository::getInstance();
        $tokenRepository->create(['token' => $token]);
        return $token;
    }

    private static function signature(string $headerEncoded, string $payloadEncoded, string $secret): string
    {
        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $secret, true);
        $signatureEncoded = self::base64Url_encode($signature);
        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }

    public static function validateToken(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

        $expectedSignature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", $_ENV['JWT_SECRET'], true);
        $expectedSignatureEncoded = self::base64Url_encode($expectedSignature);

        if (!hash_equals($expectedSignatureEncoded, $signatureEncoded)) {
            return null;
        }

        $payloadJson = self::base64Url_decode($payloadEncoded);
        $payload = json_decode($payloadJson, true);

        if (isset($payload['exp']) && time() > $payload['exp']) {
            $tokenRepository = TokenRepository::getInstance();
            $tokenRepository->delete($token);
            return null;
        }

        return $payload;
    }

    public static function refreshToken(string $token, int $expiration): ?string
    {
        $payload = self::validateToken($token);
        if (is_null($payload)) {
            return null;
        }

        unset($payload['exp']);

        return self::generateToken($payload, $expiration);
    }

    private static function base64Url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64Url_decode($data)
    {
        $padding = 4 - (strlen($data) % 4);
        if ($padding < 4) {
            $data .= str_repeat('=', $padding);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
