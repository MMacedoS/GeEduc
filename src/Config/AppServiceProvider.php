<?php

namespace App\Config;

use App\Interfaces\Activitie\IAtividadeRepository;
use App\Interfaces\Bank_account\IContaBancariaRepository;
use App\Interfaces\Classrooms\IAulaRepository;
use App\Interfaces\Classrooms\ITurmaDisciplinaRepository;
use App\Interfaces\Classrooms\ITurmaRepository;
use App\Interfaces\Contracts\IContratoRepository;
use App\Interfaces\Coordination\ICoordenadorRepository;
use App\Interfaces\Coordination\ICoordenadorTurmaRepository;
use App\Interfaces\Discipline\IDisciplinaRepository;
use App\Interfaces\File\IArquivoRepository;
use App\Interfaces\Frequencies\IFrequenciaRepository;
use App\Interfaces\MonthlyFees\IMensalidadeRepository;
use App\Interfaces\Period\IPeriodoRepository;
use App\Interfaces\Permission\IPermissaoRepository;
use App\Interfaces\Person\IPessoaContatoRepository;
use App\Interfaces\Person\IPessoaFisicaRepository;
use App\Interfaces\Plan\IPlanoRepository;
use App\Interfaces\Profile\IUsuarioRecuperarSenhaRepository;
use App\Interfaces\Profile\IUsuarioRepository;
use App\Interfaces\Scores\INotaRepository;
use App\Interfaces\Scores\IParalelaRepository;
use App\Interfaces\Site\Album\ISiteAlbumRepository;
use App\Interfaces\Site\Archive\ISiteArquivoRepository;
use App\Interfaces\Site\Carousel\ISiteCarrosselRepository;
use App\Interfaces\Site\Event\ISiteEventoRepository;
use App\Interfaces\Student\IEstudanteMensalidadeRepository;
use App\Interfaces\Student\IEstudanteRepository;
use App\Interfaces\Student\IEstudanteTurmaRepository;
use App\Interfaces\Teacher\IProfessorDisciplinaRepository;
use App\Interfaces\Teacher\IProfessorRepository;
use App\Interfaces\Ticket\IBoletoRepository;
use App\Interfaces\Work_Load\ICargaHorariaRepository;
use App\Repositories\Activitie\AtividadeRepository;
use App\Repositories\Bank_account\ContaBancariaRepository;
use App\Repositories\Classrooms\AulaRepository;
use App\Repositories\Classrooms\TurmaDisciplinaRepository;
use App\Repositories\Classrooms\TurmaRepository;
use App\Repositories\Contracts\ContratoRepository;
use App\Repositories\Coordination\CoordenadorRepository;
use App\Repositories\Coordination\CoordenadorTurmaRepository;
use App\Repositories\Discipline\DisciplinaRepository;
use App\Repositories\File\ArquivoRepository;
use App\Repositories\Frequencies\FrequenciaRepository;
use App\Repositories\MonthlyFees\MensalidadeRepository;
use App\Repositories\Period\PeriodoRepository;
use App\Repositories\Permission\PermissaoRepository;
use App\Repositories\Person\PessoaContatoRepository;
use App\Repositories\Person\PessoaFisicaRepository;
use App\Repositories\Plan\PlanoRepository;
use App\Repositories\Profile\UsuarioRecuperarSenhaRepository;
use App\Repositories\Profile\UsuarioRepository;
use App\Repositories\Scores\NotaRepository;
use App\Repositories\Scores\ParalelaRepository;
use App\Repositories\Site\Album\SiteAlbumRepository;
use App\Repositories\Site\Archive\SiteArquivoRepository;
use App\Repositories\Site\Carousel\SiteCarrosselRepository;
use App\Repositories\Site\Event\SiteEventoRepository;
use App\Repositories\Student\EstudanteMensalidadeRepository;
use App\Repositories\Student\EstudanteRepository;
use App\Repositories\Student\EstudanteTurmaRepository;
use App\Repositories\Teacher\ProfessorDisciplinaRepository;
use App\Repositories\Teacher\ProfessorRepository;
use App\Repositories\Ticket\BoletoRepository;
use App\Repositories\Work_Load\CargaHorariaRepository;

class AppServiceProvider 
{
    protected $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function registerDependencies() {
        // Registra as dependências
        $this->container
            ->set(
                IUsuarioRepository::class, 
                new UsuarioRepository()
        );
        $this->container
            ->set(
                IUsuarioRecuperarSenhaRepository::class,
                new UsuarioRecuperarSenhaRepository()
            );
        $this->container
            ->set(
                IEstudanteRepository::class,
                new EstudanteRepository()
            );        
        $this->container
            ->set(
                IEstudanteTurmaRepository::class,
                new EstudanteTurmaRepository()
            );        
        $this->container
            ->set(
                IEstudanteMensalidadeRepository::class,
                new EstudanteMensalidadeRepository()
            );
    
        $this->container
            ->set(
                IFrequenciaRepository::class,
                new FrequenciaRepository()
            );        
        $this->container
            ->set(
                IArquivoRepository::class,
                new ArquivoRepository()
            );
        
        $this->container
            ->set(
                IMensalidadeRepository::class,
                new MensalidadeRepository()
            );
        
        $this->container
            ->set(
                IPeriodoRepository::class,
                new PeriodoRepository()
            );
        
        $this->container
            ->set(
                IPessoaFisicaRepository::class,
                new PessoaFisicaRepository()
            );
        
        $this->container
            ->set(
                IPessoaContatoRepository::class,
                new PessoaContatoRepository()
            );
                
        $this->container
            ->set(
                IPessoaFisicaRepository::class,
                new PessoaFisicaRepository()
            );
                
        $this->container
            ->set(
                IPermissaoRepository::class,
                new PermissaoRepository()
            );
        
        $this->container
            ->set(
                IPlanoRepository::class,
                new PlanoRepository()
            );
        
        $this->container
            ->set(
                INotaRepository::class,
                new NotaRepository()
            );
        
        $this->container
            ->set(
                IParalelaRepository::class,
                new ParalelaRepository()
            );

        $this->container
            ->set(
                ISiteAlbumRepository::class,
                new SiteAlbumRepository()
            );        
        
        $this->container
            ->set(
                ISiteEventoRepository::class,
                new SiteEventoRepository()
            );

        $this->container
            ->set(
                ISiteArquivoRepository::class,
                new SiteArquivoRepository()
            );        
        
        $this->container
            ->set(
                ISiteCarrosselRepository::class,
                new SiteCarrosselRepository()
            );
    
        $this->container
            ->set(
                IProfessorRepository::class,
                new ProfessorRepository()
            );
                
        $this->container
            ->set(
                IProfessorDisciplinaRepository::class,
                new ProfessorDisciplinaRepository()
            );
                
        $this->container
            ->set(
                ICargaHorariaRepository::class,
                new CargaHorariaRepository()
            );
                        
        $this->container
            ->set(
                IContaBancariaRepository::class,
                new ContaBancariaRepository()
            );
                        
        $this->container
            ->set(
                IAtividadeRepository::class,
                new AtividadeRepository()
            );
        
        $this->container
            ->set(
                ITurmaRepository::class,
                new TurmaRepository()
            );        
                        
        $this->container
            ->set(
                ITurmaDisciplinaRepository::class,
                new TurmaDisciplinaRepository()
            );        
                        
        $this->container
            ->set(
                ICoordenadorRepository::class,
                new CoordenadorRepository()
            );
                                
        $this->container
            ->set(
                ICoordenadorTurmaRepository::class,
                new CoordenadorTurmaRepository()
            );

        $this->container
            ->set(
                IDisciplinaRepository::class,
                new DisciplinaRepository()
            );
        
        $this->container
            ->set(
                IContratoRepository::class,
                new ContratoRepository()
            );

        $this->container
            ->set(
                IBoletoRepository::class,
                new BoletoRepository()
            );

        $this->container
            ->set(
                IAulaRepository::class,
                new AulaRepository()
            );
    }
}