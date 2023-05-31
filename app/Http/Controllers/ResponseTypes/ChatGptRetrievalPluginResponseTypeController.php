<?php

namespace App\Http\Controllers\ResponseTypes;

use App\Models\Outbound;
use App\Models\ResponseType;
use App\ResponseType\ResponseTypeEnum;

class ChatGptRetrievalPluginResponseTypeController extends BaseResponseTypeController
{

    public function create(Outbound $outbound)
{
    $response = ResponseType::create([
        'type' => ResponseTypeEnum::ChatGptRetrievalPlugin,
        'order' => $outbound->response_types->count() + 1,
        'outbound_id' => $outbound->id,
        'prompt_token' => [],
        'meta_data' => [],
    ]);

        request()->session()->flash('flash.banner', 'Response Type created, update settings 👉');

            return to_route('response_types.chatgptretrievalplugin.edit', [
                'outbound' => $outbound->id,
                'response_type' => $response->id,
            ]);
    }

    public function edit(Outbound $outbound, ResponseType $response_type)
{
    return inertia('ResponseTypes/ChatGptRetrievalPlugin/Edit', [
        'response_type' => $response_type,
        'outbound' => $outbound,
        'details' => config('larachain.response_types.chatgptretrievalplugin'),
    ]);
}

    public function update(Outbound $outbound, ResponseType $response_type)
{
    $validated = request()->validate(
        [
            'meta_data.SOMETHING' => ['required'],
        ]
    );

    $response_type->meta_data = $validated['meta_data'];
    $response_type->save();

    request()->session()->flash('flash.banner', 'Updated 📀📀📀📀');

    return to_route('outbounds.'.$outbound->type->value.'.show', [
        'outbound' => $outbound->id,
        'project' => $outbound->project->id,
    ]);
}


    public function store(Outbound $outbound)
    {
        // TODO: Implement store() method.
    }


}
