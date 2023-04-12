<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Trasa;
use App\Models\Friend;
use App\Models\Location;
use App\Models\UsersTrasas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuthApiController extends Controller
{
    //
    public function login(Request $request)
    {

        $email = $request->input('email');
        $password = $request->input('password');

        // Sprawdzanie czy dane logowania są poprawne
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            // Jeśli tak, zwracanie odpowiedniej odpowiedzi
            Log::info('Funkcja logowania została uruchomiona succes');
            
            return response()->json(['status' => 'success', 'message' => 'Zalogowano pomyślnie']);
        } else {
            // Jeśli nie, zwracanie odpowiedzi z błędem
            Log::info('Funkcja logowania została uruchomiona error');
            return redirect('/api/register/token?data='.json_encode($request->all()));
        }
    }

    public function register(Request $request)
    {
        // Pobieranie danych rejestracyjnych z żądania
        $name = $request->input('nick');
        $email = $request->input('email');
        $password = $request->input('password');
    
        // Tworzenie nowego użytkownika
        
        $user = new User;
        $user->name = $name;
        $user->email = $email;
        $user->password = bcrypt($password);
        
        $user->save();
    
        Log::info('Użytkownik utworzony: ' . $name);
    
        return redirect('/api/sanctum/token?data='.json_encode($request->all()));

        // Zwracanie odpowiedzi z sukcesem
        /*
        session()->put('data', [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'name' => $request->input('nick')
        ]);
        return redirect('/api/sanctum/token');
        */
        //return redirect('/api/sanctum/token')->with('data', $request->all());
        // return response()->json(['status' => 'success', 'message' => 'Rejestracja zakończona pomyślnie']);
        
    }

    public function findUser(Request $request)
    {
        $name = $request->input('nick');
        Log::info('Użytkownik wyszukiwany: ' . $name);
        $users = User::where('name', $name)->get();
        Log::info('wyszukani użytkownicy: ' . $users);
        return response()->json($users);
    }

    public function addFriend(Request $request){
        $userId = $request->input('user_id');
        $friendId = $request->input('friend_id');
        Log::info('Użytkownik wyszukiwany: ' . $userId);
        Log::info('Znajomy wyszukiwany: ' . $friendId);
        $friend = new Friend();
        $friend->user_id = $userId;
        $friend->friend_id = $friendId;
        $friend->status = 0;
        $friend->save();
        Log::info('Relacja w bazie : ' . $friend);
        return response()->json($friend);
    }

    public function userFriend(Request $request){
        $id = $request->input('user_id');
        Log::info('szukane id : ' . $id);
        $friend = Friend::where('user_id', $id)->where('status', 1)->get();
        $friends_list = [];
        $friend->each(function ($single_friend) use (&$friends_list) {
            $user = $single_friend->friend;
            $friends_list[] = $user;
            Log::info('Odpowidź z bazy user : ' . $user);
        });
        $friend = Friend::where('friend_id', $id)->where('status', 1)->get();
        $friend->each(function ($single_friend) use (&$friends_list) {
            $user = $single_friend->user;
            $friends_list[] = $user;
            Log::info('Odpowidź z bazy user : ' . $user);
        });
        $final_friends_list = $friends_list;
        //Log::info('Odpowidź z bazy friend list : ' . $final_friends_list);
        //Log::info('Odpowidź z bazy user : ' . $user);
        return response()->json( $final_friends_list);
    }

    public function userRequest(Request $request){
        $id = $request->input('user_id');
        //Log::info('szukane id : ' . $id);
        $friend = Friend::where('friend_id', $id)->where('status', 0)->get();
        $friends_list = [];
        $friend->each(function ($single_friend) use (&$friends_list) {
            $user = $single_friend->user;
            $friends_list[] = $user;
            //Log::info('Odpowidź z bazy user : ' . $user);
        });
        $final_friends_list = $friends_list;
        //Log::info('Odpowidź z bazy friend list : ' . $final_friends_list);
        //Log::info('Odpowidź z bazy user : ' . $user);
        return response()->json( $final_friends_list);
    }

    public function userRequestStatus(Request $request){
        $user_id = $request->input('user_id');
        //Log::info('user id : '.$user_id);
        
        $friend_id = $request->input('friend_id');
        //Log::info('friend_id : '.$friend_id);
        $friend = new Friend();
        $friend->updateStatus($user_id, $friend_id,1);
        return response()->json($friend);
    }

    public function addFriendToTrase(Request $request){
        Log::info('Add friend to trase request : '.$request);
        $trasaData = $request->only(['nazwa', 'id_tworcyOnSerwer', 'czy_zakonczona','tworca','przewidywana_dlugosc','przewidywany_czas']);
        $lokalizacjeData = $request->only(['lokalizacje']);
        $trasaDataToCheck = $request->only(['nazwa', 'id_tworcyOnSerwer']);
        $trasa = Trasa::firstOrNew( $trasaDataToCheck);
        if($trasa->exists){
            //Log::info('Add Location to trase request : '.$lokalizacjeData);
            foreach($request->lokalizacje as $lokalizacja) {
                //Log::info('Add Location to trase request : '.$lokalizacja->nazwa);
                $location = Location::firstOrNew(['trasa_id' => $trasa->id, 'dlugosc' => $lokalizacja['dlugosc'], 'szerokosc'=>$lokalizacja['szerokosc']]);
                Log::info('Add Location to trase request : '.$location);
                $location->fill($lokalizacja);
                $location->save();
            }
            $user = User::find($request->dodany_znajomy);
            if (!$trasa->users->contains($user)) 
            {
                $trasa->users()->attach($user);
            }
        }else{
            $trasa = new Trasa();
            $user = User::find($request->dodany_znajomy);
            $trasa->fill($trasaData);
            $trasa->save();
            $trasa->users()->attach($user);
            /*foreach($lokalizacjeData as $lokalizacja) {
                $location = new Location();
                $location->fill($lokalizacja);
                $location->idTrasy = $trasa->id;
                $location->save();
            }*/
        }
        //$trasa->fill($trasaData);
       /* $trasa->save();
        foreach($lokalizacjeData as $lokalizacja) {
            $location = new Location();
            $location->fill($lokalizacja);
            $location->idTrasy = $trasa->id;
            $location->save();
        }*/

        //Log::info('dodanie trasy '.$trasa);
    }

    public function getMyTrase(Request $request){
        $id = $request->input('user_id');
        Log::info("Trasy dla użytkownika o id :" .$id);
        $user = User::find($id);
        Log::info("Trasy dla użytkownika model użytkownika :" .$user);
        $trasy = $user->trasy()->with('lokalizacje')->get();
        
        //Log::info("Trasy dla użytkownika model trasy :" .$trasy);
        return response()->json($trasy);
    }

    public function getUserToTrase(Request $request){
        $user_id = $request->input('user_id');
        $nazwa = $request->input('nazwa');
        Log::info("user id : " .$user_id);
        Log::info("nazwa trasy : " .$nazwa);
        
        if( Trasa::Where('id_tworcyOnSerwer', $user_id)->where('nazwa', $nazwa)->exists())
        { 
        $trasa = Trasa::Where('id_tworcyOnSerwer', $user_id)->where('nazwa', $nazwa);
        $trasaArray = $trasa->get()->toArray();
        $trasaId = $trasaArray[0]['id'];
        $Users = UsersTrasas::Where('trasa_id', $trasaId)->get();
        Log::info("wyszukane if  : " .$trasaId);
        return response()->json($Users);
        }else if(UsersTrasas::Where( 'users_id' , $user_id)->exists()){
            $Users = UsersTrasas::Where('users_id', $user_id);
            $UsersArray = $Users->get()->toArray();
            $trasa_id = $UsersArray[0]['trasa_id'];
            $trasa = Trasa::Where('id', $trasa_id)->get();
            Log::info(' Wyszukana Trasa : ' . $trasa_id);
            
            $result = [];
            foreach ($trasa as $t) {
                $result[] = [
                    'users_id' => $t->id_tworcyOnSerwer,
                ];
            }
            return response()->json($result);

        }
       
       
    }
}
