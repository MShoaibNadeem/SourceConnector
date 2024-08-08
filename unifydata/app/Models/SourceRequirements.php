<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class SourceRequirements extends Model
{
    use HasFactory;
    protected $fillable = ['type', 'name', 'requirements'];

    public static function checkTemplate($type,$name){
        // check for templae
        $requirements = self::where('type', $type)->where('name', $name)->first();

        if ($requirements) {
            // Return the stored requirements
            return response()->json(json_decode($requirements->requirements, true));
        }

        return false;


    }

    public static function getSourceReq($id)
    {
        $source=self::where('_id',$id)->get();
        return $source;
    }

    public static function createTemplate($type,$name,$requirements){
                // Ensure $type and $name are JSON encoded if they are arrays/objects
                $typeJson = is_array($type) || is_object($type) ? json_encode($type) : $type;
                $nameJson = is_array($name) || is_object($name) ? json_encode($name) : $name;

                self::create([
                    'type' => $typeJson,
                    'name' => $nameJson,
                    'requirements' => json_encode($requirements)
                ]);
    }
}
