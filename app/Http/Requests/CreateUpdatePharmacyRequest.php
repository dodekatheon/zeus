<?php

namespace App\Http\Requests;

use App\Models\Pharmacy;
use Illuminate\Foundation\Http\FormRequest;

class CreateUpdatePharmacyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user()->is_admin) {
            return true;
        }

        if (empty($this->route('pharmacy'))) {
            return false;
        }

        return $this->user()->id === $this->route('pharmacy')->owner_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:pharmacies|max:255',
            'region' => 'required',
            'area' => 'nullable',
            'address' => 'required',
            'additional_address' => 'nullable',
            'phone' => 'nullable|digits:8',
            'home_phone' => 'nullable|digits:8',
            'am' => 'nullable|digits_between:1,10|unique:pharmacies',
            'owner_id' => 'nullable|exists:users,id'
        ];
    }
}
