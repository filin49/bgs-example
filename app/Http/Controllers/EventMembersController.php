<?php

namespace App\Http\Controllers;

use App\Jobs\SendWelcomeEmail;
use App\Models\EventMembers;
use Illuminate\Http\Request;
use Mockery\Exception;


/**
 * @OA\Info(title="BGS Group Test API", version="0.1")
 * Class EventMembersController
 * @package App\Http\Controllers
 */

/**
 * @OA\SecurityScheme(
 *   securityScheme="token",
 *   type="apiKey",
 *   name="Bearer",
 *   in="header"
 * )
 */
class EventMembersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @OA\Get(
     *     path="/api/members",
     *     summary="All members list",
     *     tags={"members"},
     *     security={{"token": {}}},
     *     @OA\Response(
     *          response=200,
     *          description="Complete response",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="members",
     *                      type="array",
     *                      @OA\Items(type="object"),
     *                      example="[{'id':...}]"
     *                  )
     *              )
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Error: Unauthorized",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                      example="error"
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Unauthorized"
     *                  )
     *              )
     *          ),
     *     )
     * )
     * @OA\Get(
     *     path="/api/members?event={event_id}",
     *     summary="Members list by event_id",
     *     tags={"members"},
     *     security={{"token": {}}},
     *     @OA\Parameter(
     *         name="event_id",
     *         in="path",
     *         description="Event id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Complete response",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="members",
     *                      type="array",
     *                      @OA\Items(type="object"),
     *                      example="[{'id':...}]"
     *                  )
     *              )
     *          ),
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $members = EventMembers::query()
            ->select(['id', 'name', 'surname', 'email'])
            ->when(!empty($request->input('event')), function($query) use ($request) {
                return $query->where('event_id', $request->input('event'));
            })->get();
        return response()->json([
            'members' => $members
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/members",
     *     summary="Add new member",
     *     tags={"members"},
     *     security={{"token": {}}},
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="surname",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="event_id",
     *                      type="integer"
     *                  ),
     *                  example={"name": "John", "surname": "Doe", "email": "doej@bgs-group.test", "event_id": 1}
     *              )
     *          )
     *
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Complete response",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="member",
     *                      type="array",
     *                      @OA\Items(type="object"),
     *                      example="[{'id':...}]"
     *                  )
     *              )
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Error: Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                      example="error"
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Bad request"
     *                  )
     *              )
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Error: Unauthorized",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                      example="error"
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Unauthorized"
     *                  )
     *              )
     *          ),
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            if (empty($request->input('name')))
                throw new Exception('Field "name" is required');
            if (empty($request->input('surname')))
                throw new Exception('Field "surname" is required');
            if (empty($request->input('email')))
                throw new Exception('Field "email" is required');
            if (empty($request->input('event_id')))
                throw new Exception('Field "event_id" is required');
            if (EventMembers::query()->where('email', $request->input('email'))->count() > 0) {
                throw new Exception('Email already registered');
            }
            $member = new EventMembers($request->only('name', 'surname', 'email', 'event_id'));
            $member->save();
            SendWelcomeEmail::dispatch($member->toArray());
            return response()->json([
                'member' => $member
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/members/{id}",
     *     summary="Get member details by Id",
     *     tags={"members"},
     *     security={{"token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Member id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Complete response",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="member",
     *                      type="array",
     *                      @OA\Items(type="object"),
     *                      example="{'id':...}"
     *                  )
     *              )
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Error: Member not found",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                      example="error"
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Member not found"
     *                  )
     *              )
     *          ),
     *     ),
     * )
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $member = EventMembers::query()->find($id);
        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member not found'], 400);
        }
        return response()->json([
            'member' => $member
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/members/{id}",
     *     summary="Update member details",
     *     tags={"members"},
     *     security={{"token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Member id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="name",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="surname",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="event_id",
     *                      type="integer"
     *                  ),
     *                  example={"name": "John", "surname": "Doe", "email": "doej@bgs-group.test", "event_id": 1}
     *              )
     *          )
     *
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Complete response",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="member",
     *                      type="array",
     *                      @OA\Items(type="object"),
     *                      example="[{'id':...}]"
     *                  )
     *              )
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Error: Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                      example="error"
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Bad request"
     *                  )
     *              )
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Error: Unauthorized",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                      example="error"
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Unauthorized"
     *                  )
     *              )
     *          ),
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $member = EventMembers::query()->find($id);
        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member not found'], 400);
        }
        try {
            if (EventMembers::query()
                    ->where('email', $request->input('email'))
                    ->where('id', '<>', $id)
                    ->count() > 0
            ) {
                throw new Exception('Email already registered');
            }
            $member->fill($request->only('name', 'surname', 'email', 'event_id'));
            $member->save();
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
        return response()->json([
            'member' => $member
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/members/{id}",
     *     summary="Delete member",
     *     tags={"members"},
     *     security={{"token": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Member id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int32"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Complete response",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                      example="success"
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Member deleted succefuly"
     *                  )
     *              )
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Error: Bad request",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                      example="error"
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Bad request"
     *                  )
     *              )
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Error: Unauthorized",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="status",
     *                      type="string",
     *                      example="error"
     *                  ),
     *                  @OA\Property(
     *                      property="message",
     *                      type="string",
     *                      example="Unauthorized"
     *                  )
     *              )
     *          ),
     *     )
     * )
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        try {
            if (EventMembers::query()->find($id) === null) {
                throw new Exception('Member not found');
            }
            EventMembers::query()->where('id', $id)->delete();
            return response()->json(['status' => 'success', 'message' => 'Member deleted succefuly']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
