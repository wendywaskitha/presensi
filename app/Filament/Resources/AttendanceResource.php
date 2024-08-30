<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Attendance;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AttendanceResource\Pages;
use AymanAlhattami\FilamentDateScopesFilter\DateScopeFilter;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Kehadiran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user')
                    ->label('Nama Pegawai')
                    ->relationship('user','name')
                    ->disabled()
                    ->columnSpanFull()
                    ->required(),
                Section::make()->schema([
                    Forms\Components\TextInput::make('schedule_latitude')
                        ->label('Latitude Kantor')
                        ->required()
                        ->disabled()
                        ->numeric(),
                    Forms\Components\TextInput::make('schedule_longitude')
                        ->label('Longitude Kantor')
                        ->required()
                        ->disabled()
                        ->numeric(),
                ])->columns(2),
                Section::make()->schema([
                    Forms\Components\TextInput::make('schedule_start_time')
                        ->label('Jam Jadwal Masuk')
                        ->disabled()
                        ->required(),
                    Forms\Components\TextInput::make('schedule_end_time')
                        ->label('Jam Jadwal Pulang')
                        ->required()
                        ->disabled(),
                ])->columns(2),

                Section::make()->schema([
                    Forms\Components\TextInput::make('start_latitude')
                        ->label('Latitude Awal Masuk')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('start_longitude')
                        ->label('Longitude Awal Masuk')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('end_latitude')
                        ->label('Latitude Akhir Pulang')
                        ->numeric(),
                    Forms\Components\TextInput::make('end_longitude')
                        ->label('Longitude Akhir Pulang')
                        ->numeric(),
                    Forms\Components\TimePicker::make('start_time')
                        ->label('Waktu Datang')
                        ->required(),
                    Forms\Components\TimePicker::make('end_time')
                        ->label('Waktu Pulang')
                        ->required(),
                ])->columns(2),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $is_super_admin = Auth::user()->hasRole('super_admin');

                if (!$is_super_admin) {
                    $query->where('user_id', Auth::user()->id);
                }

            })
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pegawai')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_late')
                    ->label('Status')
                    ->getStateUsing(function($record) {
                        return $record->isLate() ? 'Terlambat' : 'Tepat Waktu';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Terlambat' => 'danger',
                        'Tepat Waktu' => 'success',
                    })
                    ->description(fn (Attendance $record): string => 'Durasi : '.$record->workDuration()),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Waktu Datang'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Waktu Pulang'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at','desc')
            ->headerActions([
                FilamentExportHeaderAction::make('export')
            ])
            ->filters([
                DateRangeFilter::make('created_at')->label('Periode Laporan'),
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
