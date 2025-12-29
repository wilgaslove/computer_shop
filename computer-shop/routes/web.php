use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});
