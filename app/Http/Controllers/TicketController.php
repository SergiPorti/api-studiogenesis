<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function getTickets(Request $request)
    {
        $tickets = Ticket::where('user_id', '=', $request->user()->id)->get();
        if (!$tickets) {
            return response()->json(["data" => ["message" => "No s'han trobat tickets"]], 204);
        }
        return response()->json(['data' => ["tickets" => $tickets]], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'event_date' => 'nullable|string',
            'price' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(["data" => ['error_message' => $validator->errors(), 'message' => 'Error al crear el tiquet']], 422);
        }
        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        $ticket = Ticket::create($data);

        if ($ticket) {
            if ($request->has('images')) {
                $files = $request->file('images');
                foreach ($files as $file) {
                    $name = time() . $file->getClientOriginalExtension();
                    $path = public_path('images');
                    $file->move(public_path('images'), $name);
                    $image = new TicketImage();
                    $image->ticket_id = $ticket->id;
                    $image->path = $path . '/' . $name;
                    $image->save();
                }
            }
            return response()->json(["data" => ['message' => 'Ticket creat correctament']], 200);
        }

        return response()->json(["data" => ['message' => 'Error al crear el ticket']], 500);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:tickets,id',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'event_date' => 'nullable|string',
            'price' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(["data" => ['error_message' => $validator->errors(), 'message' => 'Error al editar el tiquet']], 422);
        }

        $ticket = Ticket::find($request->id);
        $ticket->update($validator->validated());
        if ($request->has('images')) {
            $files = $request->file('images');
            foreach ($files as $file) {
                $name = time() . $file->getClientOriginalExtension();
                $path = public_path('images');
                $file->move(public_path('images'), $name);
                $image = new TicketImage();
                $image->ticket_id = $ticket->id;
                $image->path = $path . '/' . $name;
                $image->save();
            }
        }

        if ($ticket) {
            return response()->json(["data" => ['message' => 'Tiquet creat correctament']], 200);
        }

        return response()->json(["data" => ['message' => 'Error al crear el tiquet']], 500);
    }

    public function delete(Request $request)
    {
        try {
            $delete = Ticket::find($request->id)->delete();
            return $this->getTickets($request);
        } catch (\Exception $e) {
            return response()->json(["data" => ["message" => "Hi ha hagut un error eliminant el tiquet", "error_message" => $e]], 422);
        }
    }

    public function search(Request $request)
    {
        if (empty($request->pharam)) {
            return $this->getTickets($request);
        }

        $validator = Validator::make($request->all(), [
            'pharam' => 'required|string',
        ]);


        if ($validator->fails()) {
            return response()->json(["data" => ['error_message' => $validator->errors(), 'message' => 'Error al filtrar el tiquet']], 422);
        }


        $query = strtolower($request->pharam);

        $tickets = Ticket::where('name', 'like', '%' . $query . '%')->get();
        return response()->json(["data" => ['tickets' => $tickets]], 200);
    }
}
