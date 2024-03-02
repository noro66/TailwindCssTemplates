public function store(Request $request)
{
// user validation
$userData = $request->validate(
[
'name' => ['required', 'min:3', 'max:16', Rule::unique('users', 'name')],
'email' => ['required', 'email', Rule::unique('users', 'email')],
'password' => ['required', 'min:3', 'max:16', 'same:Cpassword'],
'Cpassword' => ['required', 'min:3', 'max:16'],
'profile_image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
'role' => [],
],

[

'name.required' => 'Name is required.',
'name.min' => 'The name must have more than 3 characters.',
'name.max' => 'The name must have less than 16 characters.',
'name.unique' => 'This name is already taken.',
'email.required' => 'The email is required.',
'email.email' => 'Incorrect email structure.',
'email.unique' => 'This email is already taken.',
'password.min' => 'The password must have more than 3 characters.',
'password.max' => 'The password must have less than 16 characters.',
'password.required' => 'The password is required.',
'Cpassword.min' => 'The password must have more than 3 characters.',
'Cpassword.max' => 'The password must have less than 16 characters.',
'Cpassword.required' => 'The password is required.',
'profile_image.required' => 'The image is required.',
'profile_image.image' => 'The file must be an image.',
'profile_image.mimes' => 'The image must be of type: jpeg, png, jpg, gif, or webp.',
'profile_image.max' => 'The image must not be larger than 2048 kilobytes.',
]
);
if ($userData['role'] === 'passager') {
// passager validation
$passagerData = $request->validate([
'phone' => 'required',

], [
'phone.required' => 'phone is required.',

]);
} else if ($userData['role'] === 'chauffeur') {
// Driver validation

$chauffeurData = $request->validate([
'Description' => 'required',
'immatricule' => 'required',
'Desponability' => 'required|in:Available,Reserved,In Use',
'VoitureType' => 'required|in:Taxi,Honda,Bus',
'TypeDePayment' => 'required|in:Cash,Carte',
], [
'Description.required' => 'Description is required.',
'immatricule.required' => 'Car registration is required.',
'Desponability.*' => 'Availability is required Or invalide data.',
'VoitureType.*' => 'Car Type is required Or invalide data.',
'TypeDePayment.*' => 'Payment Type is required Or invalide data.',
]);
}



if ($userData['password'] !== $userData['Cpassword']) {
return redirect('/passenger')->with('error', 'Password and confirmation password do not match.');
}

$userData['password'] = bcrypt($userData['password']);

if ($request->hasFile('profile_image')) {
$file = $request->file('profile_image');
$pictureName = time() . '.' . $file->extension();
$file->storeAs('public/image', $pictureName);
$userData['profile_image'] = $pictureName;
}

// Create user
$user = User::create($userData);
// Assign user_id create passager

if ($userData['role'] == 'passager') {


$passagerData['user_id'] = $user->id;
passager::create($passagerData);
auth()->login($user);

return redirect('/passager');
} else if ($userData['role'] == 'chauffeur') {
// Assign user_id create Chauffeur


$chauffeurData['user_id'] = $user->id;
Chauffeur::create($chauffeurData);
auth()->login($user);

return redirect('/chauffeur');
}
}