<?php

declare(strict_types=1);

namespace Pest\Mutate\Mutators\Sets;

use Pest\Mutate\Contracts\MutatorSet;
use Pest\Mutate\Mutators\Unwrap\UnwrapChop;
use Pest\Mutate\Mutators\Unwrap\UnwrapChunkSplit;
use Pest\Mutate\Mutators\Unwrap\UnwrapHtmlentities;
use Pest\Mutate\Mutators\Unwrap\UnwrapHtmlEntityDecode;
use Pest\Mutate\Mutators\Unwrap\UnwrapHtmlspecialchars;
use Pest\Mutate\Mutators\Unwrap\UnwrapHtmlspecialcharsDecode;
use Pest\Mutate\Mutators\Unwrap\UnwrapLcfirst;
use Pest\Mutate\Mutators\Unwrap\UnwrapLtrim;
use Pest\Mutate\Mutators\Unwrap\UnwrapMd5;
use Pest\Mutate\Mutators\Unwrap\UnwrapNl2br;
use Pest\Mutate\Mutators\Unwrap\UnwrapRtrim;
use Pest\Mutate\Mutators\Unwrap\UnwrapStripTags;
use Pest\Mutate\Mutators\Unwrap\UnwrapStrIreplace;
use Pest\Mutate\Mutators\Unwrap\UnwrapStrPad;
use Pest\Mutate\Mutators\Unwrap\UnwrapStrRepeat;
use Pest\Mutate\Mutators\Unwrap\UnwrapStrReplace;
use Pest\Mutate\Mutators\Unwrap\UnwrapStrrev;
use Pest\Mutate\Mutators\Unwrap\UnwrapStrShuffle;
use Pest\Mutate\Mutators\Unwrap\UnwrapStrtolower;
use Pest\Mutate\Mutators\Unwrap\UnwrapStrtoupper;
use Pest\Mutate\Mutators\Unwrap\UnwrapSubstr;
use Pest\Mutate\Mutators\Unwrap\UnwrapTrim;
use Pest\Mutate\Mutators\Unwrap\UnwrapUcfirst;
use Pest\Mutate\Mutators\Unwrap\UnwrapUcwords;
use Pest\Mutate\Mutators\Unwrap\UnwrapWordwrap;

class UnwrapSet implements MutatorSet
{
    /**
     * {@inheritDoc}
     */
    public static function mutators(): array
    {
        return [
            UnwrapChop::class,
            UnwrapChunkSplit::class,
            UnwrapHtmlentities::class,
            UnwrapHtmlEntityDecode::class,
            UnwrapHtmlspecialchars::class,
            UnwrapHtmlspecialcharsDecode::class,
            UnwrapLcfirst::class,
            UnwrapLtrim::class,
            UnwrapMd5::class,
            UnwrapNl2br::class,
            UnwrapRtrim::class,
            UnwrapStripTags::class,
            UnwrapStrIreplace::class,
            UnwrapStrPad::class,
            UnwrapStrRepeat::class,
            UnwrapStrReplace::class,
            UnwrapStrrev::class,
            UnwrapStrShuffle::class,
            UnwrapStrtolower::class,
            UnwrapStrtoupper::class,
            UnwrapSubstr::class,
            UnwrapTrim::class,
            UnwrapUcfirst::class,
            UnwrapUcwords::class,
            UnwrapWordwrap::class,
        ];
    }
}
