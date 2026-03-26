<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Roles
        $adminRoleId = DB::table("roles")->insertGetId(["name" => "Administrator", "created_at" => $now, "updated_at" => $now]);
        $viewerRoleId = DB::table("roles")->insertGetId(["name" => "Viewer", "created_at" => $now, "updated_at" => $now]);

        // 2. Users
        DB::table("users")->insert([
            "name" => "Admin User",
            "email" => "admin@example.com",
            "password" => Hash::make("password"),
            "role_id" => $adminRoleId,
            "created_at" => $now,
            "updated_at" => $now,
        ]);

        DB::table("users")->insert([
            "name" => "Test Viewer",
            "email" => "viewer@example.com",
            "password" => Hash::make("password"),
            "role_id" => $viewerRoleId,
            "created_at" => $now,
            "updated_at" => $now,
        ]);

        // 3. Cities
        $cityId1 = DB::table("cities")->insertGetId(["postal_code" => "1000", "name" => "Brussels", "created_at" => $now, "updated_at" => $now]);
        $cityId2 = DB::table("cities")->insertGetId(["postal_code" => "9000", "name" => "Ghent", "created_at" => $now, "updated_at" => $now]);
        $cityId3 = DB::table("cities")->insertGetId(["postal_code" => "2000", "name" => "Antwerp", "created_at" => $now, "updated_at" => $now]);

        // 4. Addresses
        $addrId1 = DB::table("addresses")->insertGetId([
            "street" => "Wetstraat",
            "house_number" => "1",
            "city_id" => $cityId1,
            "location_data" => DB::raw("ST_GeomFromText('POINT(4.3686 50.8466)', 4326)"), // Brussels
            "created_at" => $now,
            "updated_at" => $now
        ]);
        $addrId2 = DB::table("addresses")->insertGetId([
            "street" => "Veldstraat",
            "house_number" => "25",
            "city_id" => $cityId2,
            "location_data" => DB::raw("ST_GeomFromText('POINT(3.7226 51.0537)', 4326)"), // Ghent
            "created_at" => $now,
            "updated_at" => $now
        ]);
        $addrId3 = DB::table("addresses")->insertGetId([
            "street" => "Meir",
            "house_number" => "50",
            "city_id" => $cityId3,
            "location_data" => DB::raw("ST_GeomFromText('POINT(4.4069 51.2181)', 4326)"), // Antwerp
            "created_at" => $now,
            "updated_at" => $now
        ]);

        // 5. Teachers
        $teacherId1 = DB::table("teachers")->insertGetId([
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "john.doe@example.com",
            "company_number" => "BE0123456789",
            "telephone" => "021234567",
            "cellphone" => "0470123456",
            "address_id" => $addrId1,
            "created_at" => $now,
            "updated_at" => $now,
        ]);

        $teacherId2 = DB::table("teachers")->insertGetId([
            "first_name" => "Jane",
            "last_name" => "Smith",
            "email" => "jane.smith@example.com",
            "company_number" => "BE0987654321",
            "telephone" => "092234567",
            "cellphone" => "0470987654",
            "address_id" => $addrId2,
            "created_at" => $now,
            "updated_at" => $now,
        ]);

        // 6. Course Types
        $typeId1 = DB::table("course_types")->insertGetId(["name" => "Workshop", "created_at" => $now, "updated_at" => $now]);
        $typeId2 = DB::table("course_types")->insertGetId(["name" => "Evening Class", "created_at" => $now, "updated_at" => $now]);
        $typeId3 = DB::table("course_types")->insertGetId(["name" => "Online Course", "created_at" => $now, "updated_at" => $now]);

        // 7. Courses
        $courseId1 = DB::table("courses")->insertGetId(["name" => "Laravel Beginners", "created_at" => $now, "updated_at" => $now]);
        $courseId2 = DB::table("courses")->insertGetId(["name" => "Advanced Vue.js", "created_at" => $now, "updated_at" => $now]);
        $courseId3 = DB::table("courses")->insertGetId(["name" => "Angular Mastery", "created_at" => $now, "updated_at" => $now]);

        // Pivot tables: course_teacher and course_course_type
        DB::table("course_teacher")->insert([
            ["course_id" => $courseId1, "teacher_id" => $teacherId1],
            ["course_id" => $courseId2, "teacher_id" => $teacherId2],
            ["course_id" => $courseId3, "teacher_id" => $teacherId1],
            ["course_id" => $courseId3, "teacher_id" => $teacherId2],
        ]);

        DB::table("course_course_type")->insert([
            ["course_id" => $courseId1, "course_type_id" => $typeId1],
            ["course_id" => $courseId2, "course_type_id" => $typeId2],
            ["course_id" => $courseId3, "course_type_id" => $typeId3],
        ]);

        // 8. Certificates
        DB::table("certificates")->insert([
            ["name" => "Laravel Certified Developer", "created_at" => $now, "updated_at" => $now],
            ["name" => "Frontend Master", "created_at" => $now, "updated_at" => $now],
            ["name" => "Fullstack Professional", "created_at" => $now, "updated_at" => $now],
        ]);
    }
}
