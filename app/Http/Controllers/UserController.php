<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->validated();

            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            $token = $user->createToken('auth_token')->plainTextToken;

            $sector = $user->sector()->create([
                'name' => "Home",
                'user' => $user->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'token' => $token,
                    ],
                    'sector' => [$sector],
                ]
            ], Response::class::HTTP_CREATED);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Kesalahan saat registrasi' . $th,
            ], Response::class::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(UserRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->validated();

            $user = User::where('email', $data['email'])->first();

            if (!$user || !Hash::check($data['password'], $user->password)) {
                if (!$user) {
                    $errors = ['email' => ['Email tidak terdaftar']];
                } else {
                    $errors = ['password' => ['Password salah']];
                }

                return response()->json([
                    'status' => false,
                    'message' => 'Login gagal, silahkan coba lagi.',
                    'errors' => $errors,
                ], Response::class::HTTP_UNAUTHORIZED);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $sector = $user->sector;
            $device = Device::where('sector_id', $sector[0]->id)->first();

            return response()->json([
                'status' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'token' => $token,
                    ],
                    'sector' => $sector,
                    'device' => $device,
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan dalam login' .$th,
            ], Response::class::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
