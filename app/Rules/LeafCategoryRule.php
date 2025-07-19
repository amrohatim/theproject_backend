<?php

namespace App\Rules;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class LeafCategoryRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // If value is empty, let the required rule handle it
        if (empty($value)) {
            return;
        }

        // Find the category
        $category = Category::find($value);

        // If category doesn't exist, let the exists rule handle it
        if (!$category) {
            return;
        }

        // Check if the category can be selected for products
        if (!$category->canBeSelectedForProducts()) {
            if ($category->isRootCategory()) {
                $fail('Please select a subcategory, not a main category.');
            } elseif ($category->isParentCategory()) {
                $fail('Please select a specific subcategory, not a category group.');
            } else {
                $fail('The selected category is not valid for product assignment.');
            }
        }
    }
}
