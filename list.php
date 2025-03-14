public function buscarYCambiarCampus($campus)
{
    // Mapeo de equivalencias
    $campusMap = [
        "FUNIBER 3"  => "_WEBFUB3_",
        "FUNIBER 4+" => "_WEBFUB4P_"
    ];

    // Separar los campuses del JSON y convertirlos
    $campusesArray = explode(',', $campus['campuses']);
    $formattedCampuses = array_map(fn($item) => trim($campusMap[$item] ?? $item), $campusesArray);

    // Obtener el valor actual de la base de datos
    $registro = DB::table('programa_version')
        ->where('abreviatura', $campus['abreviatura'])
        ->first();

    if ($registro) {
        // Decodificar el JSON existente en la base de datos
        $currentCampuses = json_decode($registro->campus, true) ?? [];

        // Agregar los nuevos campuses sin duplicados
        foreach ($formattedCampuses as $newCampus) {
            if (!in_array($newCampus, $currentCampuses)) {
                $currentCampuses[] = $newCampus;
            }
        }

        // Guardar el nuevo array como JSON en la BD
        DB::table('programa_version')
            ->where('abreviatura', $campus['abreviatura'])
            ->update(['campus' => json_encode($currentCampuses)]);
    }
}
