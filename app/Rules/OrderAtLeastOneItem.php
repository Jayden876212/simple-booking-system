<?php

namespace App\Rules;

use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

class OrderAtLeastOneItem implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $items = $value;
        
        $at_least_one_item_found = false;
        foreach ($items as $quantity) {
            if ($quantity != 0) {
                $at_least_one_item_found = true;
            }
        }
        if (! $at_least_one_item_found) {
            $fail("You must order at least one item.");
        }
    }
}
