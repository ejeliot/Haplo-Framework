<?php
    /**
     * Framework Specific Exceptions
     *
     * This file is part of the Haplo Framework, a simple PHP MVC framework
     *
     * Copyright (C) 2008-2011, Brightfish Software Limited/Ed Eliot
     *
     * For the full copyright and license information, please view the LICENSE
     * file that was distributed with this source code
     *
     * @package HaploExceptions
     **/ 
    
    class HaploUndefinedException extends Exception {}
    class HaploMethodNotFoundException extends Exception {}
    class HaploActionNotFoundException extends Exception {}
    class HaploLibraryNotFoundException extends Exception {}
    class HaploConfigParseFileException extends Exception {}
    class HaploConfigKeyNotFoundException extends Exception {}
    class HaploConfigSectionNotFoundException extends Exception {}
    class HaploRouterNoActionDefinedException extends Exception {}
    class HaploRouterNoRedirectUrlDefinedException extends Exception {}
    class HaploRouterActionTypeNotSupportedException extends Exception {}
    class HaploNoDefault404DefinedExceoption extends Exception {}
    class HaploPhpConfigException extends Exception {}
    class HaploDirNotFoundException extends Exception {}
    class HaploDirNotWriteableException extends Exception {}
    class HaploCloningNotAllowedEception extends Exception {}
    class HaploInvalidTemplateException extends Exception {}
    class HaploPostFilterFunctionNotFoundException extends Exception {}
    class HaploTemplateNotFoundException extends Exception {}
    class HaploLangFileNotFoundException extends Exception {}
    class HaploTranslationKeyNotFoundException extends Exception {}
    class HaploMethodNotImplementedException extends Exception {}
    class HaploNonceMismatchException extends Exception {}
    class HaploEmptyParameterException extends Exception {}
    class HaploInvalidParameterException extends Exception {}
    class HaploClassNotFoundException extends Exception {}
?>