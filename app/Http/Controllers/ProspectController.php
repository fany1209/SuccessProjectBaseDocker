<?php
/*
prospects
28/07/25
stefany
Actualizado por: Jacob
Fecha de actualización: 09-09-2025
*/
namespace App\Http\Controllers;

use App\Helpers\DatabaseErrors;
use App\Http\Requests\StoreProspectRequest;
use Illuminate\Http\Request;
use App\Models\Prospect;
use App\Models\Sector;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ProspectController extends Controller
{
    public function index(){
        $sectors = Sector::all();  
        return view('prospect', compact('sectors'));
    }

    public function getProspects(Request $request){
        $sector = $request->input('sector');
        $search = $request->input('search');
        $city = $request->input('city');
        $state = $request->input('state');
        $name = $request->input('name');
        $rfc = $request->input('rfc');

        $query = DB::table('prospects')->join('sectors','sectors.sector_id','=','prospects.sector_id')
            ->select(
                'prospects.prospect_id',
                'sectors.name as sector',
                'prospects.name',
                'prospects.phone',
                'prospects.email',
                'prospects.rfc',
                DB::raw("concat(prospects.address,', ',prospects.district,', ',prospects.city,', ',prospects.state) as address")
            );
            
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('sectors.name', 'like', '%' . $search . '%')
                ->orWhere('prospects.name', 'like', '%' . $search . '%')
                ->orWhere('prospects.phone', 'like', '%' . $search . '%')
                ->orWhere('prospects.email', 'like', '%' . $search . '%')
                ->orWhere('prospects.rfc', 'like', '%' . $search . '%');
            });
        }
        if (!empty($sector)) {
            $query->where('prospects.sector_id', $sector);
        }
        if (!empty($city)) {
            $query->where('prospects.city', 'like', '%' . $city . '%');
        }
        if (!empty($state)) {
            $query->where('prospects.state', 'like', '%' . $state . '%');
        }
        if (!empty($name)) {
            $query->where('prospects.name', 'like', '%' . $name . '%');
        }
        if (!empty($rfc)) {
            $query->where('prospects.rfc', 'like', '%' . $rfc . '%');
        }
        
        $prospects = $query->get();
        $prospectsWithPermissions = $prospects->map(function ($query){
            return[
                'prospect_id' => $query->prospect_id,
                'sector' => $query->sector,
                'name' => $query->name,
                'phone' => $query->phone,
                'email' => $query->email,
                'rfc' => $query->rfc,
                'address' => $query->address,
                'canUpdate' => auth()->user()->can('prospects.update'),
                'canDelete' => auth()->user()->can('prospects.delete'),
            ];
        });
        return response()->json(['prospects'=>$prospectsWithPermissions]);
    }

    public function destroy($id){
        try{
            DB::transaction(function () use ($id){
                $prospect = Prospect::find($id);
                if($prospect) {
                    $prospect->delete();
                    return response()->json(['success' => true, 'message' => 'Prospect deleted']);
                } else {
                    return response()->json(['success' => false, 'message' => 'Prospect not deleted'], 404);
                }
            });
        }catch(QueryException $e){
            return DatabaseErrors::handle($e);
        }
    }

    public function show($id){
        $prospect = Prospect::where('prospect_id',$id)->first();
        return response()->json(['prospect'=> $prospect->only(['prospect_id','sector_id','name','phone','email','rfc','state','city','district','address'])]);
    }

    public function update(StoreProspectRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                Prospect::where('prospect_id', $request->prospect_id)
                    ->update($request->only([
                        'sector_id', 'name', 'phone', 'email', 'rfc', 
                        'state', 'city', 'district', 'address'
                    ]));
            });

            return response()->json(['message' => 'Operation successfully made'], 201);

        } catch (QueryException $e) {
            return DatabaseErrors::handle($e);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocurrió un error inesperado.'
            ], 500);
        }
    }

    public function store(StoreProspectRequest $request){
        try{
            return DB::transaction(function () use ($request){
                Prospect::create($request->only(['sector_id','name','phone','email','rfc','state','city','district','address']));
                return response()->json(['message' => 'Operation successfuly make it'], 201);
            });
        }catch(QueryException $e){
            return DatabaseErrors::handle($e);
        }
    }
}
