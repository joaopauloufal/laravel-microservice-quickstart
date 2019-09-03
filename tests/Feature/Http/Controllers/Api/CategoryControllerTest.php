<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route("categories.index"));

        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]);
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route("categories.show",['category'=>$category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
    }

    public function testInvalidationData(){
        $response = $this->json('post',route("categories.store"),[]);

        $this->assertInvalidationRequired($response);

        $response = $this->json('post',route("categories.store"),
            [
                "name"=>str_repeat("a",256),
                "is_active"=>"a"
        ]);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);


        $category = factory(Category::class)->create();
        $response = $this->json('put',route("categories.update",
            [
                "category"=>$category->id
            ]),
            []
        );
        $this->assertInvalidationRequired($response);

        $response = $this->json('put',route("categories.update",[
            "category"=>$category->id
            ]),
            [
                "name"=>str_repeat("a",256),
                "is_active"=>"a"
            ]);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

    }

    public function assertInvalidationRequired(TestResponse $response){
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(["is_active"])
            ->assertJsonFragment([
                \Lang::trans("validation.required",["attribute"=>"name"])
            ]);
    }

    public function assertInvalidationMax(TestResponse $response){
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                \Lang::trans("validation.max.string",["attribute"=>"name","max"=>255])
            ]);

    }
    public function assertInvalidationBoolean(TestResponse $response){
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment([
                \Lang::trans("validation.boolean",["attribute"=>"is active"])
            ]);
    }

    public function testStore(){
        $response = $this->json('post',route("categories.store"),[
            "name" => "test"
        ]);

        $id = $response->json("id");
        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());

        $this->assertTrue($response->json("is_active"));
        $this->assertNull($response->json("description"));

        $response = $this->json('post',route("categories.store"),[
            "name" => "test2",
            "is_active" => false,
            "description" => 'description'
        ]);

        $id = $response->json("id");
        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());

        $this->assertFalse($response->json("is_active"));
        $this->assertEquals($response->json("description"),'description');
    }

    public function testUpdate(){
        $category = factory(Category::class)->create([
            "is_active"=>false,
            'description'=>"description"
        ]);
        $response = $this->json('put',route("categories.update",['category'=>$category->id]),[
            "name" => "test",
            "description"=>'test',
            "is_active" => true

        ]);

        $id = $response->json("id");
        $category = Category::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'description'=>"test",
                'is_active'=>true
            ]);

        $response = $this->json('put',route("categories.update",['category'=>$category->id]),[
            "name" => "test",
            "description"=>'',
        ]);

        $response
            ->assertJsonFragment([
                'description'=>null,
            ]);
    }

    public function testDelete(){
        $category = factory(Category::class)->create();
        $response = $this->json('delete',route("categories.destroy",['category'=>$category->id]));


        $category = Category::find($category->id);
        $response->assertStatus(204);
        $this->assertNull($category);



    }
}
