<?php

namespace App\Http\Requests\Order\Order;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;
use App\Enums\OrderEnum;

class StoreRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Customer info - required from FE
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_name' => 'required|string|max:100',

            // Items - required from FE (BE sẽ tính totals từ items)
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',

            // Shipping address - required
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_ward' => 'nullable|string|max:100',
            'shipping_street' => 'nullable|string|max:100',
            'shipping_country_code' => 'nullable|string|size:2',
            'shipping_postal_code' => 'nullable|string|max:20',

            // Shipping method
            'shipping_method' => ['nullable', 'string', Rule::in(OrderEnum::AVAILABLE_SHIPPING_METHODS)],
            'shipping_provider' => 'nullable|string|max:50',

            // Payment
            'payment_method' => ['required', 'string', Rule::in(OrderEnum::AVAILABLE_PAYMENT_METHODS)],

            // Notes
            'customer_note' => 'nullable|string',

            // Optional: FE có thể gửi shipping_fee, discount_amount
            'shipping_fee' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
        ];
    }

    public function prepareForValidation()
    {
        $keys = [
            'customerName' => 'customer_name',
            'customerEmail' => 'customer_email',
            'customerPhone' => 'customer_phone',
            'shippingAddress' => 'shipping_address',
            'shippingCity' => 'shipping_city',
            'shippingWard' => 'shipping_ward',
            'shippingStreet' => 'shipping_street',
            'shippingMethod' => 'shipping_method',
            'paymentMethod' => 'payment_method',
            'customerNote' => 'customer_note',
            'shippingFee' => 'shipping_fee',
            'discountAmount' => 'discount_amount',
        ];
        $merges = [];
        foreach ($keys as $camel => $snake) {
            if ($this->has($camel) && !$this->has($snake)) {
                $merges[$snake] = $this->input($camel);
            }
        }
        if ($this->has('items') && is_array($this->input('items'))) {
            $items = array_map(function ($item) {
                if (isset($item['productId']) && !isset($item['product_id'])) {
                    $item['product_id'] = $item['productId'];
                }
                if (isset($item['productVariantId']) && !isset($item['product_variant_id'])) {
                    $item['product_variant_id'] = $item['productVariantId'];
                }
                return $item;
            }, $this->input('items'));
            $merges['items'] = $items;
        }

        if (!empty($merges)) {
            $this->merge($merges);
        }
    }

    public function messages()
    {
        return [];
    }
}
