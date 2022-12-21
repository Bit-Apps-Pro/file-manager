<?php

namespace BitApps\FM\Core\Http;

final class HttpHelper
{
    public static function post($url, $data, $headers = null, $options = null)
    {
        $defaultOptions = [
            'headers' => $headers,
            'body' => $data,
            'timeout' => 30,
        ];

        $options = wp_parse_args($options, $defaultOptions);
        $requestReponse = wp_remote_post($url, $options);
        if (is_wp_error($requestReponse)) {
            return $requestReponse;
        }
        // $responseCode = wp_remote_retrieve_response_code($requestReponse);
        // if (!\is_null($responseCode) && $responseCode != 200) {
        //     return wp_remote_retrieve_response_message($requestReponse);
        // }
        $responseBody = wp_remote_retrieve_body($requestReponse);
        $jsonData = json_decode($responseBody);

        return \is_null($jsonData) ? $responseBody : $jsonData;
    }

    public static function get($url, $data, $headers = null, $options = null)
    {
        $defaultOptions = [
            'headers' => $headers,
            'body' => $data,
            'timeout' => 30,
        ];
        $options = wp_parse_args($options, $defaultOptions);
        $requestReponse = wp_remote_get($url, $options);
        if (is_wp_error($requestReponse)) {
            return $requestReponse;
        }
        // $responseCode = wp_remote_retrieve_response_code($requestReponse);
        // if (!\is_null($responseCode) && $responseCode != 200) {
        //     return wp_remote_retrieve_response_message($requestReponse);
        // }
        $responseBody = wp_remote_retrieve_body($requestReponse);
        $jsonData = json_decode($responseBody);

        return \is_null($jsonData) ? $responseBody : $jsonData;
    }

    public static function request($url, $type, $data, $headers = null, $options = null)
    {
        $defaultOptions = [
            'method' => $type,
            'headers' => $headers,
            'body' => $data,
            'timeout' => 30,
        ];
        $options = wp_parse_args($options, $defaultOptions);
        $requestReponse = wp_remote_request($url, $options);
        if (is_wp_error($requestReponse)) {
            return $requestReponse;
        }
        // $responseCode = wp_remote_retrieve_response_code($requestReponse);
        // if (!\is_null($responseCode) && $responseCode != 200) {
        //     return wp_remote_retrieve_response_message($requestReponse);
        // }
        $responseBody = wp_remote_retrieve_body($requestReponse);
        $jsonData = json_decode($responseBody);

        return \is_null($jsonData) ? $responseBody : $jsonData;
    }
}
