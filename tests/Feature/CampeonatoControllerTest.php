<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Time;
use App\Models\Campeonato;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\CampeonatoController;

class CampeonatoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_simular_campeonato()
    {
        $csrfResponse = $this->getJson('/csrf-token');
        $csrfToken = $csrfResponse->json('token');
    
        Time::factory()->count(8)->create();
    
        $campeonato = Campeonato::create(['nome' => 'Campeonato Teste']);
    
        $response = $this->postJson("/campeonatos/{$campeonato->id}/simular", [], [
            'X-CSRF-TOKEN' => $csrfToken,
        ]);
    
        dump($response->json());
    
        $response->assertStatus(200);
    
        $response->assertJsonStructure([
            'message',
            'resultado' => ['vencedor', 'segundo', 'terceiro'],
            'campeonato',
        ]);
    }

    public function test_fluxo_completo_campeonato()
    {
        $csrfResponse = $this->getJson('/csrf-token');
        $csrfToken = $csrfResponse->json('token');
        
        for ($i = 1; $i <= 8; $i++) {
            $response = $this->postJson('/times', ['nome' => 'Time ' . $i], [
                'X-CSRF-TOKEN' => $csrfToken,
            ]);
            $response->assertStatus(201); 
        }

        $responseNonTime = $this->postJson('/times', ['nome' => 'Time 9'], [
            'X-CSRF-TOKEN' => $csrfToken,
        ]);
        $responseNonTime->assertStatus(400);

        $responseCampeonato = $this->postJson('/campeonatos', ['nome' => 'Campeonato Teste'], [
            'X-CSRF-TOKEN' => $csrfToken,
        ]);
        $responseCampeonato->assertStatus(201); 
        $campeonatoId = $responseCampeonato->json('id');

        $responseSimulacao = $this->postJson("/campeonatos/{$campeonatoId}/simular", [], [
            'X-CSRF-TOKEN' => $csrfToken,
        ]);
        $responseSimulacao->assertStatus(200); 

        $responseResultados = $this->getJson("/campeonatos/{$campeonatoId}/resultados");
        $responseResultados->assertStatus(200);
        $responseResultados->assertJsonStructure([
            'campeonato' => [
                'id',
                'nome',
                'resultado' => [
                    'primeiroLugar',
                    'segundoLugar',
                    'terceiroLugar',
                ],
            ],
        ]);

        $resultado = $responseResultados->json('campeonato.resultado');
        $this->assertNotNull($resultado['primeiroLugar']);
        $this->assertNotNull($resultado['segundoLugar']);
        $this->assertNotNull($resultado['terceiroLugar']);
    }

    public function test_verifica_persistencia_dados()
    {
        $csrfResponse = $this->getJson('/csrf-token');
        $csrfToken = $csrfResponse->json('token');
        
        for ($i = 1; $i <= 8; $i++) {
            $this->postJson('/times', ['nome' => 'Time ' . $i], [
                'X-CSRF-TOKEN' => $csrfToken,
            ]);
        }
        
        $responseCampeonato = $this->postJson('/campeonatos', ['nome' => 'Campeonato Teste'], [
            'X-CSRF-TOKEN' => $csrfToken,
        ]);
        $campeonatoId = $responseCampeonato->json('id');

        $this->postJson("/campeonatos/{$campeonatoId}/simular", [], [
            'X-CSRF-TOKEN' => $csrfToken,
        ]);

        $this->assertDatabaseHas('campeonatos', ['id' => $campeonatoId]);
        $this->assertDatabaseHas('resultados', ['campeonato_id' => $campeonatoId]);
    }

    public function testSimularPenaltisLogica()
    {
        $time1 = Time::factory()->make(['id' => 1, 'nome' => 'Time A', 'pontuacao' => 0]);
        $time2 = Time::factory()->make(['id' => 2, 'nome' => 'Time B', 'pontuacao' => 0]);
    
        $controller = new CampeonatoController();
    
        $reflection = new \ReflectionMethod(CampeonatoController::class, 'simularPenaltis');
        $reflection->setAccessible(true);
    
        $vencedor = $reflection->invoke($controller, $time1, $time2);
    
        $this->assertTrue(
            $vencedor->id === $time1->id || $vencedor->id === $time2->id,
            'O vencedor deve ser um dos dois times'
        );
    
        $this->assertTrue(
            $vencedor->id === $time1->id || $vencedor->id === $time2->id,
            'O vencedor deve ser um dos dois times'
        );
    }
    
    
}