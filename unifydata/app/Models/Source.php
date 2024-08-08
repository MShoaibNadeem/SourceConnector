<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Source extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'config'];

    protected $casts = [
        'config' => 'array',
    ];

    public static function createSource($request,$image,$configurations)
    {
        $type = $request['type'];
        $name = $request['name'];
        $userId="abc123456";//dumy id if user logined then Auth()->id
        try {
            self::create([
                'name' => $name,
                'type' => $type,
                'user_id'=>$userId,
                'image'=>$image,
                'config' => json_encode($configurations),
            ]);
            return Response::success('Source successfuly created',200);
        } catch (\Exception $e) {
            return Response::error('Error creating source',$e->getMessage(),400);
        }
    }
}
