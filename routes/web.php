<?php

use App\Http\Controllers\CrossbarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoubleDeepController;
use App\Http\Controllers\DoubleDeepCrossbarController;
use App\Http\Controllers\DoubleDeepFloorController;
use App\Http\Controllers\DoubleDeepFloorReinforcementController;
use App\Http\Controllers\DoubleDeepFramesController;
use App\Http\Controllers\DoubleDeepMenuFrameController;
use App\Http\Controllers\DoubleDeepMenuJoistController;
use App\Http\Controllers\DoubleDeepMiniatureFrameController;
use App\Http\Controllers\DoubleDeepSpacerController;
use App\Http\Controllers\DoubleDeepStructuralFrameworksController;
use App\Http\Controllers\DoubleDeepTypeBox25JoistController;
use App\Http\Controllers\DoubleDeepTypeBox2JoistController;
use App\Http\Controllers\DoubleDeepTypeC2JoistController;
use App\Http\Controllers\DoubleDeepTypeChairJoistController;
use App\Http\Controllers\DoubleDeepTypeL25JoistController;
use App\Http\Controllers\DoubleDeepTypeL2JoistController;
use App\Http\Controllers\DoubleDeepTypeLRJoistController;
use App\Http\Controllers\DoubleDeepTypeStructuralJoistController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\FloorReinforcementController;
use App\Http\Controllers\FramesController;
use App\Http\Controllers\FreightController;
use App\Http\Controllers\GrillController;
use App\Http\Controllers\MenuFrameController;
use App\Http\Controllers\MenuJoistController;
use App\Http\Controllers\PasarelaController;
use App\Http\Controllers\DriveInController;
use App\Http\Controllers\DriveInPiezasController;
use App\Http\Controllers\EstanteriaController;
// use App\Http\Controllers\MiniatureFrameController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\QuestionaryChartController;
use App\Http\Controllers\QuotationAdministrativeController;
use App\Http\Controllers\QuotationChairJoistGalvanizedPanelController;
use App\Http\Controllers\QuotationChairJoistLPaintedPanelController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\QuotationInstallController;
use App\Http\Controllers\QuotationPairBarsProtectorController;
use App\Http\Controllers\QuotationProtectorController;
use App\Http\Controllers\QuotationSpecialController;
use App\Http\Controllers\QuotationTwoInJoistLGalvanizedPanelController;
use App\Http\Controllers\QuotationTwoInJoistLPaintedPanelController;
use App\Http\Controllers\QuotationTwoPointFiveInJoistLGalvanizedPanelController;
use App\Http\Controllers\QuotationTwoPointFiveInJoistLPaintedPanelController;
use App\Http\Controllers\QuotationUninstallController;
use App\Http\Controllers\RedaccionController;
use App\Http\Controllers\SelectivoController;
use App\Http\Controllers\SinglePieceController;
use App\Http\Controllers\SpacerController;
use App\Http\Controllers\StructuralFrameworksController;
use App\Http\Controllers\TypeBox25JoistController;
use App\Http\Controllers\TypeBox2JoistController;
use App\Http\Controllers\TypeC2JoistController;
use App\Http\Controllers\TypeChairJoistController;
use App\Http\Controllers\TypeL25JoistController;
use App\Http\Controllers\TypeL2JoistController;
use App\Http\Controllers\TypeLJoistController;
use App\Http\Controllers\TypeLRJoistController;
use App\Http\Controllers\TypeStructuralJoistController;
use App\Http\Controllers\WoodController;
use App\Http\Controllers\CartController;

use App\Http\Controllers\ReportsController;
use App\Models\QuestionaryChart;
use App\Models\Quotation;
use App\Models\QuotationInstall;
use App\Models\QuotationProtector;
use App\Models\SinglePiece;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(route('login'));
});

Route::group(['middleware' => ['auth:sanctum'], 'verified'], function()
{
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('open_request', [DashboardController::class, 'open_request'])->name('open_request');
    Route::get('quoter/{id}', [DashboardController::class, 'quoter'])->name('quoter');
    Route::post('rack_engineering', [DashboardController::class, 'rack_engineering'])->name('rack_engineering');
    Route::get('rack_engineering_form/{id}', [DashboardController::class, 'rack_engineering_form'])->name('rack_engineering_form');
    
    Route::get('closing_questionary/{id}', [DashboardController::class, 'closing_questionary'])->name('closing_questionary');
    Route::post('close_quotation/{id}', [DashboardController::class, 'close_quotation'])->name('close_quotation');
    
    Route::post('cuestionario_inicial', [DashboardController::class, 'cuestionario_inicial'])->name('cuestionario_inicial');
    Route::post('material_list_engineering_form', [DashboardController::class, 'material_list_engineering_form'])->name('material_list_engineering_form');
    Route::post('layout_quoter', [DashboardController::class, 'layout_quoter'])->name('layout_quoter');
    Route::post('photos_quoter', [DashboardController::class, 'photos_quoter'])->name('photos_quoter');
    Route::post('addphotos/{id}', [DashboardController::class, 'addphotos'])->name('addphotos');
    Route::resource('questionary_charts', QuestionaryChartController::class);
    Route::get('return_material_list/{id}', [DashboardController::class, 'return_material_list'])->name('return_material_list');
    Route::post('product_menu', [DashboardController::class, 'product_menu'])->name('product_menu');

    Route::get('/selectivo', [SelectivoController::class, 'index'])->name('selectivo.index');
    Route::get('/system_menu/{id}/{type?}', [SelectivoController::class, 'show'])->name('selectivo.show');

    Route::get('/cot_marcos/{id}', [MenuFrameController::class, 'show'])->name('menuframes.show');
    Route::get('/cot_vigas/{id}', [MenuJoistController::class, 'show'])->name('menujoists.show');

    Route::get('/cot_crossbars/{id}', [CrossbarController::class, 'show'])->name('crossbars.show');
    Route::post('/cot_crossbars/calc', [CrossbarController::class, 'calc'])->name('crossbars.calc');
    
    Route::post('/cot_floors/calc', [FloorController::class, 'calc'])->name('floors.calc');
    Route::get('/cot_floors/{id}', [FloorController::class, 'show'])->name('floors.show');

    Route::post('/cot_floor_reinforcements/calc', [FloorReinforcementController::class, 'calc'])->name('floor_reinforcements.calc');
    Route::get('/cot_floor_reinforcements/{id}', [FloorReinforcementController::class, 'show'])->name('floor_reinforcements.show');
    
    Route::get('/cot_spacers/{id}', [SpacerController::class, 'show'])->name('spacers.show');
    Route::post('/cot_spacers/calc', [SpacerController::class, 'calc'])->name('spacers.calc');

    Route::get('/cot_carga_pesada/{id}', [FramesController::class, 'show'])->name('frames.show');
    Route::post('/cot_carga_pesada', [FramesController::class, 'store'])->name('frames.store');

    // Route::get('/cot_minimarcos/{id}', [MiniatureFrameController::class, 'show'])->name('miniatureframe.show');
    // Route::post('/cot_minimarcos', [MiniatureFrameController::class, 'store'])->name('miniatureframe.store');
    
    Route::get('/cot_marcos_estructurales/{id}', [StructuralFrameworksController::class, 'show'])->name('structuralframeworks.show');
    Route::post('/cot_marcos_estructurales', [StructuralFrameworksController::class, 'store'])->name('structuralframeworks.store');

    Route::get('/cot_vigas_tipo_L_2/{id}', [TypeL2JoistController::class, 'show'])->name('typel2joists.show');
    Route::post('/cot_vigas_tipo_L_2/', [TypeL2JoistController::class, 'store'])->name('typel2joists.store');
    
    Route::get('/cot_vigas_tipo_L_2/calibre14/{id}', [TypeL2JoistController::class, 'caliber14_show'])->name('typel2joists_caliber14.show');
    Route::post('/cot_vigas_tipo_L_2/calibre14', [TypeL2JoistController::class, 'caliber14_calc'])->name('typel2joists_caliber14.calc');

    Route::get('/cot_vigas_tipo_L_2_5/{id}', [TypeL25JoistController::class, 'show'])->name('typel25joists.show');
    Route::post('/cot_vigas_tipo_L_2_5/', [TypeL25JoistController::class, 'store'])->name('typel25joists.store');
    
    Route::get('/cot_vigas_tipo_L_2_5/calibre14/{id}', [TypeL25JoistController::class, 'caliber14_show'])->name('typel25joists_caliber14.show');
    Route::post('/cot_vigas_tipo_L_2_5/calibre14', [TypeL25JoistController::class, 'caliber14_calc'])->name('typel25joists_caliber14.calc');

    Route::get('/cot_vigas_tipo_Box_2/{id}', [TypeBox2JoistController::class, 'show'])->name('typebox2joists.show');
    Route::post('/cot_vigas_tipo_Box_2/', [TypeBox2JoistController::class, 'store'])->name('typebox2joists.store');
    
    Route::get('/cot_vigas_tipo_Box_2/calibre14/{id}', [TypeBox2JoistController::class, 'caliber14_show'])->name('typebox2joists_caliber14.show');
    Route::post('/cot_vigas_tipo_Box_2/calibre14', [TypeBox2JoistController::class, 'caliber14_calc'])->name('typebox2joists_caliber14.calc');

    Route::get('/cot_vigas_tipo_Box_25/{id}', [TypeBox25JoistController::class, 'show'])->name('typebox25joists.show');
    Route::post('/cot_vigas_tipo_Box_25/', [TypeBox25JoistController::class, 'store'])->name('typebox25joists.store');
    
    Route::get('/cot_vigas_tipo_Box_25/calibre14/{id}', [TypeBox25JoistController::class, 'caliber14_show'])->name('typebox25joists_caliber14.show');
    Route::post('/cot_vigas_tipo_Box_25/calibre14', [TypeBox25JoistController::class, 'caliber14_calc'])->name('typebox25joists_caliber14.calc');

    Route::get('/cot_vigas_tipo_Structural/{id}', [TypeStructuralJoistController::class, 'show'])->name('typestructuraljoists.show');
    Route::post('/cot_vigas_tipo_Structural/', [TypeStructuralJoistController::class, 'store'])->name('typestructuraljoists.store');
    
    Route::get('/cot_vigas_tipo_Structural/calibre14/{id}', [TypeStructuralJoistController::class, 'caliber14_show'])->name('typestructuraljoists_caliber14.show');
    Route::post('/cot_vigas_tipo_Structural/calibre14', [TypeStructuralJoistController::class, 'caliber14_calc'])->name('typestructuraljoists_caliber14.calc');

    Route::get('/cot_vigas_tipo_C2/{id}', [TypeC2JoistController::class, 'show'])->name('typec2joists.show');
    Route::post('/cot_vigas_tipo_C2/', [TypeC2JoistController::class, 'store'])->name('typec2joists.store');
    
    Route::get('/cot_vigas_tipo_C2/calibre14/{id}', [TypeC2JoistController::class, 'caliber14_show'])->name('typec2joists_caliber14.show');
    Route::post('/cot_vigas_tipo_C2/calibre14', [TypeC2JoistController::class, 'caliber14_calc'])->name('typec2joists_caliber14.calc');

    Route::get('/cot_vigas_tipo_LR/{id}', [TypeLRJoistController::class, 'show'])->name('typelrjoists.show');
    Route::post('/cot_vigas_tipo_LR/', [TypeLRJoistController::class, 'store'])->name('typelrjoists.store');
    
    Route::get('/cot_vigas_tipo_LR/calibre14/{id}', [TypeLRJoistController::class, 'caliber14_show'])->name('typelrjoists_caliber14.show');
    Route::post('/cot_vigas_tipo_LR/calibre14', [TypeLRJoistController::class, 'caliber14_calc'])->name('typelrjoists_caliber14.calc');

    Route::get('/cot_vigas_tipo_Chair/{id}', [TypeChairJoistController::class, 'show'])->name('typechairjoists.show');
    Route::post('/cot_vigas_tipo_Chair/', [TypeChairJoistController::class, 'store'])->name('typechairjoists.store');
    
    Route::get('/cot_vigas_tipo_Chair/calibre14/{id}', [TypeChairJoistController::class, 'caliber14_show'])->name('typechairjoists_caliber14.show');
    Route::post('/cot_vigas_tipo_Chair/calibre14', [TypeChairJoistController::class, 'caliber14_calc'])->name('typechairjoists_caliber14.calc');



    Route::get('/cot_freights/{id}', [FreightController::class, 'selectivo_show'])->name('selectivo_freights.show');
    Route::get('/cot_transports/{id}', [FreightController::class, 'selectivo_transports'])->name('selectivo_transports');
    Route::post('/cot_transports_add', [FreightController::class, 'selectivo_transports_add'])->name('selectivo_transports_add');
    Route::get('/cot_travel_assignments/{id}', [FreightController::class, 'selectivo_travel_assignments'])->name('selectivo_travel_assignments');
    Route::get('/cot_quotation_travel_assignments/{id}', [FreightController::class, 'selectivo_quotation_travel_assignments'])->name('selectivo_quotation_travel_assignments');
    Route::get('/cot_installs/{id}', [FreightController::class, 'selectivo_installs'])->name('selectivo_installs');
    Route::post('/cot_travel_assignments_add', [FreightController::class, 'selectivo_travel_assignments_add'])->name('selectivo_travel_assignments_add');
    Route::post('/cot_fiut_add', [FreightController::class, 'selectivo_fiut_add'])->name('selectivo_fiut_add');
    
    Route::get('/cot_panels/{id}', [PanelController::class, 'selectivo_panels'])->name('selectivo_panels');
    Route::get('/cot_two_in_joist_l_galvanized_panels/{id}', [PanelController::class, 'selectivo_two_in_joist_l_galvanized_panels'])->name('selectivo_two_in_joist_l_galvanized_panels');
    Route::get('/cot_two_in_joist_l_painted_panels/{id}', [PanelController::class, 'selectivo_two_in_joist_l_painted_panels'])->name('selectivo_two_in_joist_l_painted_panels');
    Route::get('/cot_two_point_five_in_joist_l_galvanized_panels/{id}', [PanelController::class, 'selectivo_two_point_five_in_joist_l_galvanized_panels'])->name('selectivo_two_point_five_in_joist_l_galvanized_panels');
    Route::get('/cot_two_point_five_in_joist_l_painted_panels/{id}', [PanelController::class, 'selectivo_two_point_five_in_joist_l_painted_panels'])->name('selectivo_two_point_five_in_joist_l_painted_panels');
    Route::get('/cot_chair_joist_galvanized_panels/{id}', [PanelController::class, 'selectivo_chair_joist_galvanized_panels'])->name('selectivo_chair_joist_galvanized_panels');
    Route::get('/cot_chair_joist_l_painted_panels/{id}', [PanelController::class, 'selectivo_chair_joist_l_painted_panels'])->name('selectivo_chair_joist_l_painted_panels');
    Route::get('/cot_protectors/{id}', [PanelController::class, 'selectivo_protectors'])->name('selectivo_protectors');
    Route::get('/cot_pair_bars_protectors/{id}', [PanelController::class, 'selectivo_pair_bars_protectors'])->name('selectivo_pair_bars_protectors');
    Route::post('/cot_two_in_joist_l_galvanized_panels/store', [PanelController::class, 'selectivo_two_in_joist_l_galvanized_panels_store'])->name('selectivo_two_in_joist_l_galvanized_panels.store');
    Route::post('/cot_two_in_joist_l_painted_panels/store', [PanelController::class, 'selectivo_two_in_joist_l_painted_panels_store'])->name('selectivo_two_in_joist_l_painted_panels.store');
    Route::post('/cot_two_point_five_in_joist_l_galvanized_panels/store', [PanelController::class, 'selectivo_two_point_five_in_joist_l_galvanized_panels_store'])->name('selectivo_two_point_five_in_joist_l_galvanized_panels.store');
    Route::post('/cot_two_point_five_in_joist_l_painted_panels/store', [PanelController::class, 'selectivo_two_point_five_in_joist_l_painted_panels_store'])->name('selectivo_two_point_five_in_joist_l_painted_panels.store');
    Route::post('/cot_chair_joist_galvanized_panels/store', [PanelController::class, 'selectivo_chair_joist_galvanized_panels_store'])->name('selectivo_chair_joist_galvanized_panels.store');
    Route::post('/cot_chair_joist_l_painted_panels/store', [PanelController::class, 'selectivo_chair_joist_l_painted_panels_store'])->name('selectivo_chair_joist_l_painted_panels.store');
    
    Route::get('/cot_grills/{id}', [GrillController::class, 'selectivo_grills_index'])->name('selectivo_grills.index');
    Route::post('/cot_grills/store', [GrillController::class, 'selectivo_grills_store'])->name('selectivo_grills.store');
    Route::get('/cot_woods/{id}', [WoodController::class, 'selectivo_woods_index'])->name('selectivo_woods.index');
    Route::post('/cot_woods/store', [WoodController::class, 'selectivo_woods_store'])->name('selectivo_woods.store');
    Route::get('/cot_special/{id}', [QuotationSpecialController::class, 'selectivo_special_index'])->name('selectivo_special.index');
    Route::post('/cot_special/store', [QuotationSpecialController::class, 'selectivo_special_store'])->name('selectivo_special.store');
    Route::get('/cot_administratives/{id}', [QuotationAdministrativeController::class, 'selectivo_administratives_index'])->name('selectivo_administratives.index');
    Route::post('/cot_administratives/store', [QuotationAdministrativeController::class, 'selectivo_administratives_store'])->name('selectivo_administratives.store');

    Route::get('/cot_protectors/{id}', [QuotationProtectorController::class, 'selectivo_protectors_index'])->name('selectivo_protectors.index');
    Route::get('/cot_protectors_create/{id}', [QuotationProtectorController::class, 'selectivo_protectors_create'])->name('selectivo_protectors.create');
    Route::post('/cot_protectors_store', [QuotationProtectorController::class, 'selectivo_protectors_store'])->name('selectivo_protectors.store');
    Route::get('/cot_protectors_edit/{id}', [QuotationProtectorController::class, 'selectivo_protectors_edit'])->name('selectivo_protectors.edit');
    Route::put('/cot_protectors_update/{id}', [QuotationProtectorController::class, 'selectivo_protectors_update'])->name('selectivo_protectors.update');
    Route::get('/cot_protectors_destroy/{id}', [QuotationProtectorController::class, 'selectivo_protectors_destroy'])->name('selectivo_protectors.destroy');

    Route::resource('quotation_installs', QuotationInstallController::class);
    Route::resource('quotation_uninstalls', QuotationUninstallController::class);

    Route::get('quotation_installs_double_deep_show/{id}', [QuotationInstallController::class, 'double_deep_show'])->name('quotation_installs_double_deep_show');
    Route::post('quotation_installs_double_deep_store', [QuotationInstallController::class, 'double_deep_store'])->name('quotation_installs_double_deep_store');
    
    Route::get('quotation_uninstalls_double_deep_show/{id}', [QuotationUninstallController::class, 'double_deep_show'])->name('quotation_uninstalls_double_deep_show');
    Route::post('quotation_uninstalls_double_deep_store', [QuotationUninstallController::class, 'double_deep_store'])->name('quotation_uninstalls_double_deep_store');
    Route::get('/selectivo_minimarcos/{id}', [MiniatureFrameController::class, 'show'])->name('miniatureframe.show');
    Route::post('/selectivo_minimarcos', [MiniatureFrameController::class, 'store'])->name('miniatureframe.store');
    
    #________________Drive in
    #Menu
    Route::get('/drivein', [DriveInController::class, 'index'])->name('drivein.index');
    Route::get('/drivein/{id}', [DriveInController::class, 'show'])->name('drivein.show');
    #Menu de marcos
    Route::get('/drivein/frames/{id}', [MenuFrameController::class, 'drive_show'])->name('menuframes.drive_show');
    #Menu de vigas
    Route::get('/drives/joist/{id}', [MenuJoistController::class, 'drive_show'])->name('menujoists.drive_show');
    #MArcos
    Route::get('/drivein_carga_pesada/{id}', [FramesController::class, 'drive_show'])->name('frames.drive_show');
    Route::post('/drivein_carga_pesada', [FramesController::class, 'drive_store'])->name('frames.drive_store');
    Route::get('drivein/shopping_cart/add_carga_pesada/{id}', [FramesController::class, 'drive_add_carrito'])->name('shopping_cart.add_drivein_carga_pesada');
    
    Route::get('/drivein_marcos_estructurales/{id}', [StructuralFrameworksController::class, 'drive_show'])->name('structuralframeworks.drive_show');
    Route::post('/drive_marcos_estructurales', [StructuralFrameworksController::class, 'drive_store'])->name('structuralframeworks.drive_store');
    #vigas
    Route::get('/drivein_vigas_tipo_L_2/{id}', [TypeL2JoistController::class, 'drive_show'])->name('typel2joists.drive_show');
    Route::post('/drivein_vigas_tipo_L_2/', [TypeL2JoistController::class, 'drive_store'])->name('typel2joists.drive_store');

    Route::get('/drivein_vigas_tipo_L_2_5/{id}', [TypeL25JoistController::class, 'drive_show'])->name('typel25joists.drive_show');
    Route::post('/drivein_vigas_tipo_L_2_5/', [TypeL25JoistController::class, 'drive_store'])->name('typel25joists.drive_store');
    

    Route::get('/drivein_vigas_tipo_Box_2/{id}', [TypeBox2JoistController::class, 'drive_show'])->name('typebox2joists.drive_show');
    Route::post('/drivein_vigas_tipo_Box_2/', [TypeBox2JoistController::class, 'drive_store'])->name('typebox2joists.drive_store');
    
    Route::get('/drivein_vigas_tipo_Box_25/{id}', [TypeBox25JoistController::class, 'drive_show'])->name('typebox25joists.drive_show');
    Route::post('/drivein_vigas_tipo_Box_25/', [TypeBox25JoistController::class, 'drive_store'])->name('typebox25joists.drive_store');
    

    Route::get('/drivein_vigas_tipo_Structural/{id}', [TypeStructuralJoistController::class, 'drive_show'])->name('typestructuraljoists.drive_show');
    Route::post('/drivein_vigas_tipo_Structural/', [TypeStructuralJoistController::class, 'drive_store'])->name('typestructuraljoists.drive_store');
    
    Route::get('/drive_in_soportes_menu/{id}', [DriveInPiezasController::class, 'soportes_menu'])->name('drive_in_soportes.menu');
    Route::get('/drive_in_soportes/{id}/{calibre}', [DriveInPiezasController::class, 'soportes_index'])->name('drive_in_soportes.index');
    Route::post('/drive_in_soportes/store', [DriveInPiezasController::class, 'soportes_store'])->name('drive_in_soportes.store');
    Route::get('/drive_in_soportes_carrito/{id}/{caliber}', [DriveInPiezasController::class, 'soportes_add_carrito'])->name('drive_in_soportes.add_carrito');
    
    Route::get('/drive_in_guias/{id}', [DriveInPiezasController::class, 'guias_index'])->name('drive_in_guias.index');
    Route::post('/drive_in_guias/store', [DriveInPiezasController::class, 'guias_store'])->name('drive_in_guias.store');
    Route::get('/drive_in_guias_carrito/{id}/', [DriveInPiezasController::class, 'guias_add_carrito'])->name('drive_in_guias.add_carrito');
    
    Route::get('/drive_in_brazos/{id}', [DriveInPiezasController::class, 'brazos_index'])->name('drive_in_brazos.index');
    Route::post('/drive_in_brazos/store', [DriveInPiezasController::class, 'brazos_store'])->name('drive_in_brazos.store');
    Route::get('/drive_in_brazos_carrito/{id}/', [DriveInPiezasController::class, 'brazos_add_carrito'])->name('drive_in_brazos.add_carrito');

    Route::get('/drive_in_arrioslados/{id}', [DriveInPiezasController::class, 'arriostrados_index'])->name('drive_in_arriostrados.index');
    Route::post('/drive_in_arrioslados/store', [DriveInPiezasController::class, 'arriostrados_store'])->name('drive_in_arriostrados.store');
    Route::get('/drive_in_arriostrados_carrito/{id}/', [DriveInPiezasController::class, 'arriostrados_add_carrito'])->name('drive_in_arriostrados.add_carrito');
    
    #________________END Drive IN

        // RUTAS PARAS PASARELA 
    Route::get('/angulos_menu/{id}', [PasarelaController::class, 'angulos_menu'])->name('pasarela_angulos.menu');
    Route::get('/angulos/{id}/{calibre}', [PasarelaController::class, 'angulos_index'])->name('pasarela_angulos.index');
    Route::post('/angulos/store', [PasarelaController::class, 'angulos_store'])->name('pasarela_angulos.store');
    Route::get('/angulos_carrito/{id}/{caliber}', [PasarelaController::class, 'angulos_add_carrito'])->name('pasarela_angulos.add_carrito');
    
    Route::get('/pasarela_galleta/{id}', [PasarelaController::class, 'galleta_show'])->name('pasarela_galleta.show');
    Route::post('/store_galleta/{id}', [PasarelaController::class, 'galleta_store'])->name('pasarela_galleta.store');
    Route::get('/pasarela_galleta_carrito/{id}', [PasarelaController::class, 'galleta_add_carrito'])->name('pasarela_galleta.add_carrito');
    
    // END PASARELA

    // RUTAS PARAS ESTANTERIA 
    Route::get('/estanteria/entrepanios/{id}/{type}', [EstanteriaController::class, 'entrepanios_index'])->name('entrepanios.index');
    Route::post('/estanteria/store', [EstanteriaController::class, 'entrepanios_store'])->name('entrepanios.store');
    Route::post('/estanteria/refuerzos', [EstanteriaController::class, 'entrepanios_refuerzos'])->name('entrepanios.update_reforcement');
    Route::get('/estanteria_entrepanios_carrito/{id}/{type}', [EstanteriaController::class, 'entrepanio_add_carrito'])->name('entrepanios.add_carrito');
    
    Route::get('/estanteria_respaldos/{id}', [EstanteriaController::class, 'respaldo_show'])->name('estanteria_respaldos.show');
    Route::post('/estanteria_respaldos/store/{id}', [EstanteriaController::class, 'respaldo_store'])->name('estanteria_respaldos.store');
    Route::get('/estanteria_respaldos/carrito/{id}', [EstanteriaController::class, 'respaldo_add_carrito'])->name('estanteria_respaldos.add_carrito');
    
    Route::get('/estanteria_escuadras/{id}', [EstanteriaController::class, 'escuadras_show'])->name('estanteria_escuadras.show');
    Route::post('/estanteria_escuadras/store/{id}', [EstanteriaController::class, 'escuadras_store'])->name('estanteria_escuadras.store');
    Route::get('/estanteria_escuadras/carrito/{id}', [EstanteriaController::class, 'escuadras_add_carrito'])->name('estanteria_escuadras.add_carrito');
    
   //END ESTANTERIA

    Route::get('/singlepieces/{id}', [SinglePieceController::class, 'show'])->name('singlepieces.show');
    Route::post('/singlepieces/calc', [SinglePieceController::class, 'calc'])->name('singlepieces.calc');

    Route::get('/quotations/show/{id}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::get('/quotations/close/{id}', [QuotationController::class, 'close'])->name('quotations.close');
    Route::get('/rpt_rack_engineering/{id}', [QuotationController::class, 'rpt_rack_engineering'])->name('rpt_rack_engineering');
    Route::get('/quotations/{id}', [QuotationController::class, 'index'])->name('quotations');
    Route::get('/shopping_cart', [CartController::class, 'index'])->name('shopping_cart.index');
    
    Route::get('shopping_cart/act', [CartController::class, 'actualizar'])->name('shopping_cart.act');
    Route::get('shopping_cart/delete/{id}', [CartController::class, 'destroy'])->name('shopping_cart.destroy');
    Route::get('shopping_cart/vaciar', [CartController::class, 'vaciar'])->name('shopping_cart.vaciar');
    
    Route::get('shopping_cart/add_protectors/{id}', [CartController::class, 'add_selectivo_protectors'])->name('shopping_cart.add_selectivo_protectors');
    Route::get('shopping_cart/add_carga_pesada/{id}', [CartController::class, 'add_selectivo_carga_pesada'])->name('shopping_cart.add_selectivo_carga_pesada');
    Route::get('shopping_cart/add_marcos_estructurales/{id}', [CartController::class, 'add_selectivo_marcos_estructurales'])->name('shopping_cart.add_selectivo_marcos_estructurales');
    Route::get('shopping_cart/add_minimarcos/{id}', [CartController::class, 'add_selectivo_minimarcos'])->name('shopping_cart.add_selectivo_minimarcos');
    
    //agregar al carrito drive in
    Route::get('shopping_cart/add_drive_frames/{id}', [CartController::class, 'add_drive_frames'])->name('shopping_cart.add_drive_frames');
    Route::get('shopping_cart/add_drive_structural_frames/{id}', [CartController::class, 'add_drive_sframes'])->name('shopping_cart.add_drive_sframes');
    Route::get('shopping_cart/add_drive_box2/{id}', [TypeBox2JoistController::class, 'add_carrito'])->name('shopping_cart.add_drive_box2');
    
    Route::get('shopping_cart/cot_floors/{id}', [FloorController::class, 'add_carrito'])->name('floors.add_carrito');

    Route::get('shopping_cart/cot_floor_reinforcements/{id}', [FloorReinforcementController::class, 'add_carrito'])->name('floor_reinforcements.add_carrito');
    
    Route::get('shopping_cart/cot_spacers/{id}', [SpacerController::class, 'add_carrito'])->name('spacers.add_carrito');
    


    Route::get('shopping_cart/add_vigas_tipo_L_2/{id}', [TypeL2JoistController::class, 'add_carrito'])->name('typel2joists.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_L_2/calibre14/{id}', [TypeL2JoistController::class, 'add_carrito14'])->name('typel2joists_caliber14.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_L_2_5/{id}', [TypeL25JoistController::class, 'add_carrito'])->name('typel25joists.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_L_2_5/calibre14/{id}', [TypeL25JoistController::class, 'add_carrito14'])->name('typel25joists_caliber14.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_Box_2/{id}', [TypeBox2JoistController::class, 'add_carrito'])->name('typebox2joists.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_Box_2/calibre14/{id}', [TypeBox2JoistController::class, 'add_carrito14'])->name('typebox2joists_caliber14.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_Box_25/{id}', [TypeBox25JoistController::class, 'add_carrito'])->name('typebox25joists.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_Box_25/calibre14/{id}', [TypeBox25JoistController::class, 'add_carrito14'])->name('typebox25joists_caliber14.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_Structural/{id}', [TypeStructuralJoistController::class, 'add_carrito'])->name('typestructuraljoists.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_Structural/calibre14/{id}', [TypeStructuralJoistController::class, 'add_carrito14'])->name('typestructuraljoists_caliber14.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_C2/{id}', [TypeC2JoistController::class, 'add_carrito'])->name('typec2joists.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_C2/calibre14/{id}', [TypeC2JoistController::class, 'add_carrito14'])->name('typec2joists_caliber14.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_LR/{id}', [TypeLRJoistController::class, 'add_carrito'])->name('typelrjoists.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_LR/calibre14/{id}', [TypeLRJoistController::class, 'add_carrito14'])->name('typelrjoists_caliber14.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_Chair/{id}', [TypeChairJoistController::class, 'add_carrito'])->name('typechairjoists.add_carrito');
    Route::get('shopping_cart/add_vigas_tipo_Chair/calibre14/{id}', [TypeChairJoistController::class, 'add_carrito14'])->name('typechairjoists_caliber14.add_carrito');

    
    Route::get('shopping_cart/cot_two_in_joist_l_galvanized_panels/{id}', [PanelController::class, 'selectivo_two_in_joist_l_galvanized_panels_add'])->name('selectivo_two_in_joist_l_galvanized_panels_add');
    Route::get('shopping_cart/cot_two_in_joist_l_painted_panels/{id}', [PanelController::class, 'selectivo_two_in_joist_l_painted_panels_add'])->name('selectivo_two_in_joist_l_painted_panels_add');
    Route::get('shopping_cart/cot_two_point_five_in_joist_l_galvanized_panels/{id}', [PanelController::class, 'selectivo_two_point_five_in_joist_l_galvanized_panels_add'])->name('selectivo_two_point_five_in_joist_l_galvanized_panels_add');
    Route::get('shopping_cart/cot_two_point_five_in_joist_l_painted_panels/{id}', [PanelController::class, 'selectivo_two_point_five_in_joist_l_painted_panels_add'])->name('selectivo_two_point_five_in_joist_l_painted_panels_add');
    Route::get('shopping_cart/cot_chair_joist_galvanized_panels/{id}', [PanelController::class, 'selectivo_chair_joist_galvanized_panels_add'])->name('selectivo_chair_joist_galvanized_panels_add');
    Route::get('shopping_cart/cot_chair_joist_l_painted_panels/{id}', [PanelController::class, 'selectivo_chair_joist_l_painted_panels_add'])->name('selectivo_chair_joist_l_painted_panels_add');

    Route::get('shopping_cart/cot_crossbars/{id}', [CrossbarController::class, 'add_carrito'])->name('crossbars.add_carrito');
    Route::get('shopping_cart/cot_grills/{id}', [GrillController::class, 'add_carrito'])->name('selectivo_grills.add_carrito');
    Route::get('shopping_cart/cot_woods/{id}', [WoodController::class, 'add_carrito'])->name('selectivo_woods.add_carrito');
    Route::get('shopping_cart/cot_special/{id}', [QuotationSpecialController::class, 'add_carrito'])->name('selectivo_special.add_carrito');
    Route::get('shopping_cart/cot_administratives/{id}', [QuotationAdministrativeController::class, 'add_carrito'])->name('selectivo_administratives.add_carrito');
    
    Route::get('shopping_cart/cot_freights/{id}', [FreightController::class, 'fletes_add_carrito'])->name('selectivo_freights.add_carrito');
    Route::get('shopping_cart/cot_quotation_travel_assignments/{id}', [FreightController::class, 'viaticos_add_carrito'])->name('selectivo_quotation_travel_assignments.add_carrito');
    Route::get('shopping_cart/cot_installs/{id}', [FreightController::class, 'selectivo_installs_add_carrito'])->name('selectivo_installs.add_carrito');
    Route::get('redaccion/{id}', [RedaccionController::class, 'generate'])->name('redaccion');
    Route::get('reporte/{id}/{report}/{pdf}/{tipo?}', [ReportsController::class, 'generate'])->name('reports.generate');
    Route::get('reporte/index', [ReportsController::class, 'index'])->name('reports.index');
    
});

