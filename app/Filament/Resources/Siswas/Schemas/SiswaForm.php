<?php

namespace App\Filament\Resources\Siswas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden; // Tambah import ini bray
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Hash;

class SiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        // DROPDOWN USER DENGAN FITUR TAMBAH OTOMATIS
                        Select::make('user_id')
                            ->label('Nama Akun (User)')
                            ->relationship('user', 'nama_lengkap')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('nama_lengkap')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255),
                                    
                                TextInput::make('username')
                                    ->label('Username')
                                    ->required()
                                    ->unique('users', 'username'),
                                    
                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->unique('users', 'email'),
                                    
                                TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->required()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)),

                                // 🔥 FIX: PAKSA ROLE SISWA DI SINI 🔥
                                // Pake Hidden biar gak bisa diganti-ganti dan gak muncul di UI
                                Hidden::make('role')
                                    ->default('siswa'),
                            ]),

                        TextInput::make('nis')
                            ->label('NIS')
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('kelas')
                            ->label('Kelas')
                            ->required(),

                        TextInput::make('jurusan')
                            ->label('Jurusan'),

                        DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir'),

                        Select::make('status')
                            ->label('Status Siswa')
                            ->options([
                                'aktif' => 'Aktif',
                                'lulus' => 'Lulus',
                                'keluar' => 'Keluar',
                            ])
                            ->default('aktif')
                            ->required(),
                    ]),
            ]);
    }
}