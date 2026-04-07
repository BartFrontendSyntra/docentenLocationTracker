<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
        $adminUserId = DB::table("users")->insertGetId([
            "name" => "Admin User",
            "email" => "admin@example.com",
            "password" => Hash::make("password"),
            "role_id" => $adminRoleId,
            "created_at" => $now,
            "updated_at" => $now,
        ]);

        DB::table("users")->insertGetId([
            "name" => "Test Viewer",
            "email" => "viewer@example.com",
            "password" => Hash::make("password"),
            "role_id" => $viewerRoleId,
            "created_at" => $now,
            "updated_at" => $now,
        ]);

        // 3. Cities (Limburg, Belgium)
        $cityId1 = DB::table("cities")->insertGetId(["postal_code" => "3500", "name" => "Hasselt", "created_at" => $now, "updated_at" => $now]);
        $cityId2 = DB::table("cities")->insertGetId(["postal_code" => "3600", "name" => "Genk", "created_at" => $now, "updated_at" => $now]);
        $cityId3 = DB::table("cities")->insertGetId(["postal_code" => "3800", "name" => "Sint-Truiden", "created_at" => $now, "updated_at" => $now]);
        $cityId4 = DB::table("cities")->insertGetId(["postal_code" => "3700", "name" => "Tongeren", "created_at" => $now, "updated_at" => $now]);

        // 4. Addresses
        $addrId1 = DB::table("addresses")->insertGetId([
            "street" => "Kuringersteenweg",
            "house_number" => "173",
            "city_id" => $cityId1,
            "location_data" => DB::raw("ST_GeomFromText('POINT(50.935076991639804 5.32144757724275)', 4326)"), // Hasselt
            "created_at" => $now,
            "updated_at" => $now
        ]);
        $addrId2 = DB::table("addresses")->insertGetId([
            "street" => "Rootenstraat",
            "house_number" => "12",
            "city_id" => $cityId2,
            "location_data" => DB::raw("ST_GeomFromText('POINT(50.9661 5.5008)', 4326)"), // Genk
            "created_at" => $now,
            "updated_at" => $now
        ]);
        $addrId3 = DB::table("addresses")->insertGetId([
            "street" => "Luikersteenweg",
            "house_number" => "44",
            "city_id" => $cityId3,
            "location_data" => DB::raw("ST_GeomFromText('POINT(50.8167 5.1864)', 4326)"), // Sint-Truiden
            "created_at" => $now,
            "updated_at" => $now
        ]);
        $addrId4 = DB::table("addresses")->insertGetId([
            "street" => "Maastrichterstraat",
            "house_number" => "9",
            "city_id" => $cityId4,
            "location_data" => DB::raw("ST_GeomFromText('POINT(50.7805 5.4642)', 4326)"), // Tongeren
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
            "company_number" => "BE0897654321",
            "telephone" => "092234567",
            "cellphone" => "0470987654",
            "address_id" => $addrId2,
            "created_at" => $now,
            "updated_at" => $now,
        ]);

        $teacherId3 = DB::table("teachers")->insertGetId([
            "first_name" => "Tom",
            "last_name" => "Peeters",
            "email" => "tom.peeters@example.com",
            "company_number" => "BE0456123789",
            "telephone" => "011223344",
            "cellphone" => "0477123456",
            "address_id" => $addrId4,
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
        $courseId4 = DB::table("courses")->insertGetId(["name" => "API Security Essentials", "created_at" => $now, "updated_at" => $now]);

        // Pivot tables: course_teacher and course_course_type
        DB::table("course_teacher")->insert([
            ["course_id" => $courseId1, "teacher_id" => $teacherId1],
            ["course_id" => $courseId2, "teacher_id" => $teacherId2],
            ["course_id" => $courseId3, "teacher_id" => $teacherId1],
            ["course_id" => $courseId3, "teacher_id" => $teacherId2],
            ["course_id" => $courseId4, "teacher_id" => $teacherId3],
        ]);

        DB::table("course_course_type")->insert([
            ["course_id" => $courseId1, "course_type_id" => $typeId1],
            ["course_id" => $courseId2, "course_type_id" => $typeId2],
            ["course_id" => $courseId3, "course_type_id" => $typeId3],
            ["course_id" => $courseId4, "course_type_id" => $typeId1],
        ]);

        // 8. Certificates
        $certificateId1 = DB::table("certificates")->insertGetId([
            "name" => "Laravel Certified Developer",
            "created_at" => $now,
            "updated_at" => $now,
        ]);

        $certificateId2 = DB::table("certificates")->insertGetId([
            "name" => "Frontend Master",
            "created_at" => $now,
            "updated_at" => $now,
        ]);

        $certificateId3 = DB::table("certificates")->insertGetId([
            "name" => "Fullstack Professional",
            "created_at" => $now,
            "updated_at" => $now,
        ]);

        DB::table("certificate_teacher")->insert([
            ["certificate_id" => $certificateId1, "teacher_id" => $teacherId1],
            ["certificate_id" => $certificateId2, "teacher_id" => $teacherId2],
            ["certificate_id" => $certificateId3, "teacher_id" => $teacherId3],
            ["certificate_id" => $certificateId1, "teacher_id" => $teacherId3],
        ]);

    }
}
