<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\APITokenController;

use App\Http\Controllers\MyCustomWebSocketHandler;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//Route::post('/send-data', [WebSocketController::class, 'sendData']);
Route::post('/register', [AuthApiController::class,   'register']);
Route::get('/sanctum/token', [APITokenController::class,   'create_token']);
    Route::post('/login',  [AuthApiController::class, 'login']);
    
Route::middleware('auth:sanctum')->group(function() {

    //Route::post('/usersFind', [AuthApiController::class, 'findUser']);
    Route::post('/usersFind', [AuthApiController::class, 'findUser']);
    Route::post('/addFriend', [AuthApiController::class, 'addFriend']);
    Route::post('/userFriend', [AuthApiController::class, 'userFriend']);
    Route::post('/userFriendRequest', [AuthApiController::class, 'userRequest']);
    Route::post('/userFriendAccept', [AuthApiController::class, 'userRequestStatus']);
    Route::post('/addFriendToTrase', [AuthApiController::class, 'addFriendToTrase']);
    Route::post('/getMyTrase', [AuthApiController::class, 'getMyTrase']);
    Route::post('/getUserToTrase', [AuthApiController::class, 'getUserToTrase']);
    });
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
    });

WebSocketsRouter::webSocket('/my-websocket', App\Http\Controllers\MyCustomWebSocketHandler::class );

Route::get('/p', function() {

    event(new \App\Events\PlaygroundEvent());
    return null;
});