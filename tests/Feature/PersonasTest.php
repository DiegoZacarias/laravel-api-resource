<?php

namespace Tests\Feature;

use App\Persona;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class PersonasTest extends TestCase
{
     use RefreshDatabase, WithFaker;

     /** @test */
     public function se_pueden_listar_personas()
     {
             $personas = factory(Persona::class,2)->create();
             $this->withoutExceptionHandling();

             $response = $this->json('GET',route('personas.index'));
             $response->assertStatus(200)
                     ->assertJson([
                        [     
                              'id' => $personas[0]->id, 
                              'nombre' => $personas[0]->nombre,
                              'apellido' => $personas[0]->apellido,
                              'documento' => $personas[0]->documento,
                              'telefono' => $personas[0]->telefono 
                        ],    
                        [     
                              'id' => $personas[1]->id, 
                              'nombre' => $personas[1]->nombre,
                              'apellido' => $personas[1]->apellido,
                              'documento' => $personas[1]->documento,
                              'telefono' => $personas[1]->telefono
                        ]
                    ])
                    ->assertJsonStructure([
                        '*' => ['id', 'nombre', 'apellido', 'documento', 'telefono'],
                    ]);
     }

     /** @test */
     public function se_pueden_agregar_personas()
     {
            $fields = [
                'nombre' => 'Diego',
                'apellido' => 'Zacarias',
                'documento' => '5309590',
                'telefono' => '0991269947'
            ];

            $this->withoutExceptionHandling();

            $response = $this->json('POST',route('personas.store',$fields));
            $response->assertStatus(201)
                    ->assertJson([
                      'nombre' => 'Diego',
                      'apellido' => 'Zacarias',
                      'documento' => '5309590',
                      'telefono' => '0991269947'
                    ]);

            $this->assertDatabaseHas('personas',['nombre' => 'Diego']);
     }

     /** @test */
     public function se_puede_editar_una_persona()
     {
         $persona = factory(Persona::class)->create();

         $fields = [
            'nombre' => 'Diego',
            'telefono' => '0991269947',
            'documento' => '123456789'
         ];

         $response = $this->json('PUT',route('personas.update',$persona->id),$fields);
         $response->assertStatus(200)
                ->assertJson([
                  'nombre' => 'Diego'
                ]);

         $this->assertDatabaseHas('personas', ['nombre' => 'Diego', 'telefono' => '0991269947']);       
     }

     /** @test */
     public function se_puede_eliminar_una_persona()
     {
        $persona = factory(Persona::class)->create();

        $this->withoutExceptionHandling();
        $response = $this->json('DELETE',route('personas.destroy',$persona->id));
        $response->assertStatus(204);
        // dd($response);

        $this->assertDatabaseMissing('personas', ['nombre' => $persona->nombre]);
     }

     /** @test */
     public function se_puede_buscar_personas()
     {
         factory(Persona::class,10)->create();
         $persona = factory(Persona::class)->create(['nombre' => 'Diego']);
         
         $palabra_buscada = ['q'=>'Diego'];
         $this->withoutExceptionHandling();

         $response = $this->json('GET',route('personas.buscar',$palabra_buscada));
         $response->assertStatus(200)
                ->assertJson(
                    [
                     [ 'nombre' => 'Diego']
                    ]
                );
     }

     /** @test */
     public function persona_requires_nombre_para_crear()
     {
             $fields = [
                'nombre' => '',
                'apellido' => $this->faker->lastname,
                'documento' => '5309590',
                'telefono' => '0991269947'
             ];

             $response = $this->json('POST',route('personas.store',$fields));
             $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)      //Status 422
                    ->assertJsonPath('errors.nombre', ['El campo nombre es obligatorio.']);  
     }

     /** @test */
     public function persona_requires_documento_para_crear()
     {
             $fields = [
                'nombre' => $this->faker->name,
                'apellido' => $this->faker->lastname,
                'documento' => '',
                'telefono' => '0991269947'
             ];

            $response= $this->json('POST',route('personas.store',$fields));
            $response->assertStatus(422)
                    ->assertJsonPath('errors.documento',['El campo documento es obligatorio.']);
     }

     /** @test */
     public function persona_requires_nombre_para_actualizar()
     {
        $persona = factory(Persona::class)->create();

        $fields = [
           'nombre' => '',
           'telefono' => '0991269947',
           'documento' => '123456789'
        ];

        $response = $this->json('PUT',route('personas.update',$persona->id),$fields);
        $response->assertStatus(422)
               ->assertJsonPath('errors.nombre',['El campo nombre es obligatorio.']);
     }

     /** @test */
     public function persona_requires_documento_para_actualizar()
     {
        $persona = factory(Persona::class)->create();

        $fields = [
           'nombre' => 'diego',
           'telefono' => '0991269984',
           'documento' => ''
        ];

        $response = $this->json('PUT',route('personas.update',$persona->id),$fields);
        $response->assertStatus(422)
               ->assertJsonPath('errors.documento',['El campo documento es obligatorio.']);
     }

     /** @test */
     public function funcion_buscar_requires_q_para_buscar_personas()
     {
        $persona = factory(Persona::class)->create(['nombre' => 'Diego']);
         
         $palabra_buscada = ['q'=>''];
         $this->withoutExceptionHandling();

         $response = $this->json('GET',route('personas.buscar',$palabra_buscada));
         $response->assertStatus(200)
                ->assertJsonPath('errors.q',['El campo buscar es obligatorio.']);
                
     }
}
