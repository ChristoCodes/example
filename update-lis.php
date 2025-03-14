public function buscarYCambiarCampus($campus)
{
    $campusMap = [
        "FUNIBER 3"  => "_WEBFUB3_",
        "FUNIBER 4+" => "_WEBFUB4P_"
    ];

    $campusesArray = explode(',', $campus['campuses']);
    $formattedCampuses = array_map(fn($item) => trim($campusMap[$item] ?? $item), $campusesArray);

    // Buscar el registro en la BD
    $registro = DB::table('programa_version')
        ->where('abreviatura', $campus['abreviatura'])
        ->first();

    if ($registro) {
        // Decodificar y actualizar
        $currentCampuses = json_decode($registro->campus, true) ?? [];
        foreach ($formattedCampuses as $newCampus) {
            if (!in_array($newCampus, $currentCampuses)) {
                $currentCampuses[] = $newCampus;
            }
        }
        DB::table('programa_version')
            ->where('abreviatura', $campus['abreviatura'])
            ->update(['campus' => json_encode($currentCampuses)]);
    } else {
        // Si no existe, insertar un nuevo registro
        DB::table('programa_version')->insert([
            'abreviatura' => $campus['abreviatura'],
            'campus' => json_encode($formattedCampuses)
        ]);
    }
}
