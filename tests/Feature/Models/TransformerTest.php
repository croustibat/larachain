<?php

namespace Tests\Feature\Models;

use App\LLMModels\OpenAi\EmbeddingsResponseDto;
use App\Models\Transformer;
use App\Transformers\TransformerTypeEnum;
use Facades\App\LLMModels\OpenAi\ClientWrapper;
use Tests\Feature\SharedSetupForPdfFile;
use Tests\TestCase;

class TransformerTest extends TestCase
{
    use SharedSetupForPdfFile;

    public function test_transformer_factory()
    {
        $model = Transformer::factory()->create();
        $this->assertNotNull($model->project->id);
        $this->assertNotNull($model->project->transformers->first()->id);
        $this->assertEquals(TransformerTypeEnum::PdfTransformer, $model->type);
    }

    public function test_runs_transformers()
    {
        $data = get_fixture('embedding_response.json');

        $dto = new EmbeddingsResponseDto(
            data_get($data, 'data.0.embedding'),
            1000,
        );

        ClientWrapper::shouldReceive('getEmbedding')->once()->andReturn($dto);

        $model = Transformer::factory()->pdfTranformer()->create([
            'project_id' => $this->source->project_id,
        ]);
        $this->assertDatabaseCount('document_chunks', 0);
        $model->run();
        $this->assertDatabaseCount('document_chunks', 10);
    }
}