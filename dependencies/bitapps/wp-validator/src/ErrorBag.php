<?php
/**
 * @license MIT
 *
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPValidator;

class ErrorBag
{
    use Helpers;
    protected $errors = [];

    public function addError($role, $customMessages)
    {
        $attributeKey = $role->getInputDataContainer()->getAttributeKey();
        $roleName = $role->getRuleName();
        $paramValues = $role->getParamValues();

        $defaultPlaceholders = [
            'attribute' => $role->getInputDataContainer()->getAttributeLabel(),
            'value' => $role->getInputDataContainer()->getAttributeValue(),
        ];

        $placeholders = array_merge($paramValues, $defaultPlaceholders);

        if (is_string($roleName) && isset($customMessages[$attributeKey . '.' . $roleName])) {
            $message = $this->replacePlaceholders($placeholders, $customMessages[$attributeKey . '.' . $roleName]);
        } elseif (is_string($roleName) && isset($customMessages[$roleName])) {
            $message = $this->replacePlaceholders($placeholders, $customMessages[$roleName]);
        } else {
            $message = $this->replacePlaceholders($placeholders, $role->message());
        }
        $this->errors[$attributeKey][] = $message;
    }

    private function replacePlaceholders($placeholders, $message)
    {
        foreach ($placeholders as $key => $placeholder) {
            if (isset($placeholders[$key])) {

                if (is_object($placeholder)) {
                    $placeholder = (array) $placeholder;
                }

                if (is_array($placeholder)) {
                    $placeholder = implode(',', $placeholder);
                }
                $message = str_replace(":" . $key, $placeholder, $message);

            }
        }
        return $message;
    }

    public function getErrors($field = null)
    {
        return $this->errors;
    }

    public function hasErrors($field = null)
    {
        if ($field === null) {
            return !empty($this->errors);
        }

        return isset($this->errors[$field]) && !empty($this->errors[$field]);
    }
}
