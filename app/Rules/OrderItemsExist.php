<?php

namespace App\Rules;

use App\Models\Item;
use Closure;
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;

class OrderItemsExist implements ValidationRule
{
    protected $items;

    public function __construct(Item $items)
    {
        $this->items = $items;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $chosen_items = $value;
        
        $items_that_do_not_exist = [];
        foreach ($chosen_items as $name => $quantity) {
            $item_found = $this->items->find($name);
            if (! $item_found) {
                $items_that_do_not_exist[] = $name;
            }
        }
        if ($items_that_do_not_exist) {
            $fail("The following items do not exist: " . implode(", ", $items_that_do_not_exist));
        }
    }
}
