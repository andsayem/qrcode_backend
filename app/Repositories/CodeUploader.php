<?php

namespace App\Repositories;

use App\Models\CodeDetail;
use App\Models\SSGCodeDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CodeUploader
{
    private $inputs = [];

    private $has_error = false;
    private $errorMessages = [];

    private $validationRules = [
        //'serial' => 'required|exists:code_details,serial|unique:ssg_code_details,serial',
        //'code' => 'required|exists:code_details,final_unique_code|unique:ssg_code_details,code',
        'code' => 'required|unique:ssg_code_details,code',
    ];

    private $customMessages = [
        'required' => 'The :attribute field is required.',
        'unique' => 'The :attribute field is unique.',
        'exists' => 'The :attribute field is not exist in code request.'
    ];

    public function __construct(array $input)
    {
        $this->inputs = $input;
        //$this->checkValidation();

        if ($this->hasErrors()) {
            return;
        }

        $this->storeToDatabase();
    }

    private function checkValidation()
    {
        $validator = Validator::make($this->inputs, $this->validationRules, $this->customMessages);

        if ($validator->fails()) {
            $this->has_error = true;
            foreach ($validator->errors()->all() as $message) {
                $this->errorMessages[] = $message;
            }
        }
    }

    public function hasErrors()
    {
        return $this->has_error;
    }

    public function getErrors()
    {
        return $this->errorMessages;
    }

    private function storeToDatabase()
    {
        try {

            $codeDetails = CodeDetail::where('serial', $this->inputs['serial'])
                ->where('final_unique_code', $this->inputs['code'])
                ->first();


            if ($codeDetails) {
                $ssgCode =  SSGCodeDetail::where('code', $this->inputs['code'])->first();
                if (!$ssgCode) {
                    DB::beginTransaction();
                    $codeDetails->is_print = 1;
                    $codeDetails->save();
                    $code = new SSGCodeDetail;
                    $code->product_id = $codeDetails->product_id ?? null;
                    $code->serial = $this->inputs['serial'];
                    $code->code = $this->inputs['code'];
                    $code->request_code_id =  $codeDetails->request_code_id;
                    $code->status = 0;
                    $code->uploaded_by = auth()->user()->id;
                    $code->uploaded_ip = \Request::ip();
                    $code->save();
                    DB::commit();
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            report($e);
            $this->has_error = true;
            $this->errorMessages[] = $e->getMessage();
        }
    }
}
