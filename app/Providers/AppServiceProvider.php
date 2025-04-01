<?php

namespace App\Providers;

use App\Repositories\Contracts\EnderecoRepositoryInterface;
use App\Repositories\Contracts\FotoPessoaRepositoryInterface;
use App\Repositories\Contracts\LotacaoRepositoryInterface;
use App\Repositories\Contracts\PessoaRepositoryInterface;
use App\Repositories\Contracts\ServidorEfetivoRepositoryInterface;
use App\Repositories\Contracts\ServidorTemporarioRepositoryInterface;
use App\Repositories\Contracts\UnidadeRepositoryInterface;
use App\Repositories\EnderecoRepository;
use App\Repositories\FotoPessoaRepository;
use App\Repositories\LotacaoRepository;
use App\Repositories\PessoaRepository;
use App\Repositories\ServidorEfetivoRepository;
use App\Repositories\ServidorTemporarioRepository;
use App\Repositories\UnidadeRepository;
use App\Services\Contracts\StorageServiceInterface;
use App\Services\MinIOStorageService;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerRepositories();
        $this->app->bind(StorageServiceInterface::class, MinIOStorageService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureCarbonSerialization();
    }

    /**
     * Configura a serialização de datas para Carbon
     */
    protected function configureCarbonSerialization(): void
    {
        Carbon::serializeUsing(fn ($carbon) => $carbon->format('Y-m-d H:i:s'));
    }

        /**
     * Registra os bindings de repositórios
     */
    protected function registerRepositories(): void
    {
        $this->app->bind(PessoaRepositoryInterface::class, PessoaRepository::class);
       
        $this->app->bind(LotacaoRepositoryInterface::class, LotacaoRepository::class);
        $this->app->bind(UnidadeRepositoryInterface::class, UnidadeRepository::class);
        $this->app->bind(EnderecoRepositoryInterface::class, EnderecoRepository::class);
        $this->app->bind(FotoPessoaRepositoryInterface::class, FotoPessoaRepository::class);
        $this->app->bind(ServidorEfetivoRepositoryInterface::class, ServidorEfetivoRepository::class);
        $this->app->bind(ServidorTemporarioRepositoryInterface::class, ServidorTemporarioRepository::class);
        
    }
}
