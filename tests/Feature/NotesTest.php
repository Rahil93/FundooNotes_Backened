<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
Use App\Model\Notes;
Use App\Model\Users;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Laravel\Passport\Passport;

class NotesTest extends TestCase
{
    use WithoutMiddleware;

   /** @test */
   public function createNotesTest()
   {
       $this->withoutExceptionHandling();

       $user = factory(Users::class)->create();

       Passport::actingAs($user);

       $response = $this->withHeaders(['Content-Type'=>"application/json"])
                        ->json('POST','/api/createNote',[
                            'title' => 'PhpTutorial',
                            'description' => 'MyPhpTutrial',
                            'user_id' => $user->id
                        ]);

       $response->assertStatus(200)
                ->assertJson(['Message' => 'Note Created Successfully']);
   }

    /** @test */
   public function createNotesTest_with_no_title_and_description()
   {
    $this->withoutExceptionHandling();

    $user = factory(Users::class)->create();

    Passport::actingAs($user);

    $response = $this->withHeaders(['Content-Type'=>"application/json"])
                     ->json('POST','/api/createNote',[
                                                        'title' => '',
                                                        'description' => '',
                                                        'user_id' => $user->id
                                                    ]);

    $response->assertStatus(400)
             ->assertJson(['Message' => 'Title & description must not be empty']);
   }

   
}
