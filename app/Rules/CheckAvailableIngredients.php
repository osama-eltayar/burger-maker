<?php

namespace App\Rules;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CheckAvailableIngredients implements DataAwareRule, ValidationRule
{

    protected Collection $data ;

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $product = Product::query()
            ->where('id',$value)
            ->with('ingredients')
            ->first();

        $requestedQuantity = collect($this->data->get('products'))
            ->firstWhere('product_id',$value)['quantity'];

        if (!$product->isALLIngredientsAvailable($requestedQuantity)){
            $fail('some ingredients is not available');
        }
    }

    public function setData(array $data)
    {
        $this->data = collect($data);

        return $this;
    }
}
