<?php

namespace Tests\Feature;

use App\LLMModels\OpenAi\EmbeddingsResponseDto;
use App\Models\Message;
use App\Models\Project;
use App\Models\ResponseType;
use App\ResponseType\ResponseDto;
use App\ResponseType\Types\EmbedQuestionResponseType;
use Facades\App\LLMModels\OpenAi\ClientWrapper;
use Tests\TestCase;

class EmbedQuestionResponseTypeTest extends TestCase
{
    public function test_embeds_question()
    {
        $embeddings = get_fixture('embedding_response.json');

        $responseType = ResponseType::factory()->create();

        $project = Project::factory()->create();

        $dto = new EmbeddingsResponseDto(
            data_get($embeddings, 'data.0.embedding'),
            1000
        );

        ClientWrapper::shouldReceive('getEmbedding')
            ->once()
            ->andReturn($dto);

        $message = Message::factory()->create();

        $this->assertNull($message->embedding);

        $dto = ResponseDto::from([
            'message' => $message,
        ]);

        $embedRt = new EmbedQuestionResponseType($project, $dto);
        $results = $embedRt->handle($responseType);

        $this->assertNotNull($results->message->embedding);

    }
}
