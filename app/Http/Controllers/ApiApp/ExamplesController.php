<?php

namespace App\Http\Controllers\ApiApp;

use App\Helpers\ApiCodes;
use App\Http\Requests\ApiApp\ExampleRequest;
use App\Http\Requests\ApiApp\StoreExampleRequest;
use App\Http\Requests\ApiApp\UpdateExampleRequest;
use App\Http\Resources\ApiApp\ExampleIndividualResource;
use App\Http\Resources\ApiApp\ExampleListResource;
use App\Models\ApiApp\Example;

class ExamplesController extends ApiBaseController
{
    public function index(ExampleRequest $request)
    {
        $query = Example::query();

        $examples = $this->fetchResults($query);

        if ($examples->isEmpty()) {
            return $this->resourceNotFound(null, ApiCodes::SUCCESS);
        }

        return $this->getJsonResponse($examples, ExampleListResource::class);
    }

    public function show(ExampleRequest $request, $exampleId)
    {
        $example = Example::query()
            ->find($exampleId);

        return $example ?
            $this->getJsonResponse($example, ExampleIndividualResource::class) :
            $this->resourceNotFound();
    }

    public function store(StoreExampleRequest $request)
    {
        $example = Example::query()
            ->create($request->all());

        if (! $example) {
            return $this->generalError();
        }

        return $this->successResponse();
    }

    public function update(UpdateExampleRequest $request, $exampleCode)
    {
        $example = Example::query()
            ->find($exampleCode);

        if (! $example) {
            return $this->resourceNotFound();
        }

        if (! $example->update($request->all())) {
            return $this->generalError();
        }

        return $this->successResponse();
    }

    public function destroy(ExampleRequest $request, $exampleCode)
    {
        $example = Example::find($exampleCode);

        if (! $example) {
            return $this->resourceNotFound();
        }

        if (! $example->delete()) {
            return $this->generalError();
        }

        return $this->successResponse();
    }

}
