<x-auth-layout>
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a
                    href="/"
                    class="link-dark text-center link-offset-2 link-opacity-100 link-opacity-50-hover"
                    style="text-decoration: none;"
                >
                    <x-application-logo style="max-height: 30px;" />
                    <h4>{{ config('app.name', 'Laravel') }}</h4>
                </a>
            </div>
            <div class="card-body login-card-body">
                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <p class="login-box-msg">{{ __('Sign in to start your session') }}</p>
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="input-group mb-1">
                        <div class="form-floating">
                            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                            <x-input-label for="email" :value="__('Email')" />
                        </div>
                        <div class="input-group-text"><span class="bi bi-envelope"></span></div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="input-group mb-1">
                        <div class="form-floating">
                            <x-text-input id="password"
                                          type="password"
                                          name="password"
                                          required autocomplete="current-password" />
                            <x-input-label for="password" :value="__('Password')" />
                        </div>
                        <div class="input-group-text"><span class="bi bi-lock-fill"></span></div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="row">
                        <!-- Remember Me -->
                        <div class="col-8 d-inline-flex align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="remember_me" name="remember" />
                                <label class="form-check-label" for="flexCheckDefault"> {{ __('Remember me') }} </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <div class="d-grid gap-2">
                                <x-primary-button class="ms-3">
                                    {{ __('Log in') }}
                                </x-primary-button>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>

                    <div class="row text-center mt-2">
                        @if (Route::has('password.request'))
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                               href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif

                        @if (Route::has('register'))
                                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                   href="{{ route('register') }}">
                                    {{ __('Register a new membership') }}
                                </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-auth-layout>
