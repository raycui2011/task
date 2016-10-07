<?php
/**
 * @file
 *
 * Add/Update MailChimp list member.
 */

namespace App\Http\Requests;

class ListMemberRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return FALSE;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email_address' => 'required',
            'status_if_new' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [

        ];
    }
}
