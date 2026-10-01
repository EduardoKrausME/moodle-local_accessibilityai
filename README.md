# local_accessibilityai

Accessibility assistance for Moodle 4.5+ that combines deterministic HTML/PHP checks with semantic AI review. The plugin
is designed around a strict rule: anything that can be detected reliably from the stored HTML is analysed locally; AI is
reserved for semantic judgements that need language/context reasoning.

## Dependency

The plugin requires `local_ai_bridge >= 2026093001`:

```php
$plugin->dependencies = [
    'local_ai_bridge' => 2026093001,
];
```

Every AI request is made exclusively through:

```php
\local_ai_bridge\api::generate('accessibilityai-review', $messages);
```

There are no provider endpoints, model settings, API keys or direct OpenAI/Gemini/Claude/Ollama integrations in this
plugin.

## Supported content in 1.0

The collector audits teacher-authored course content from:

- course section summaries;
- Page content;
- Book description and chapters;
- Text and media area (`mod_label`);
- Assignment descriptions;
- Forum descriptions.

This version deliberately does **not** inspect assignment submissions, forum posts, quiz attempts, messages, private
files or other learner-generated content.

The extraction layer is isolated behind `local_accessibilityai\content\provider_interface`, so new Moodle content
sources can be added without changing the deterministic rules or AI integration.

## Deterministic checks

The local HTML engine currently detects:

- images without `alt`;
- suspicious empty `alt` when no decorative signal is detectable;
- heading level jumps and empty headings;
- possible visual-only use of heading elements (heuristic warning);
- links with no meaningful accessible text;
- generic links such as “click here”, “clique aqui”, “more” and similar;
- data tables without headers and simple header-relationship problems;
- obsolete/problematic elements such as `font`, `center`, `marquee` and `blink`;
- positive `tabindex` values;
- duplicate element IDs;
- video without locally detectable caption/subtitle/transcript indicators;
- audio without locally detectable transcript indicators;
- common embedded video iframes without nearby transcript/caption indicators;
- iframes without `title`;
- simple contrast problems only when opaque foreground and background colors are explicitly present on the same element
  through inline CSS.

Contrast is intentionally conservative. The plugin cannot infer final theme styles, inherited CSS, pseudo-elements,
transparency, background images or the browser's final computed style from stored HTML alone. For real contrast
validation, use a rendered-page accessibility tool in the browser.

## AI semantic review

The semantic payload is minimized before it reaches the bridge. It contains local item IDs, headings, link text/target
context, image alt/title/filename/context, plain text and deterministic rule IDs. Edit URLs are never supplied by AI and
are never accepted from AI responses.

The AI is asked to review:

- semantic quality of alternative text based only on available context;
- whether link text makes sense out of context;
- clarity of headings;
- unnecessarily complex language;
- possible alternative descriptions that must be verified by a human;
- suggestions for textual simplification.

Responses must be JSON and are strictly parsed, item IDs are whitelisted to the current request batch, markup is
stripped and model-provided URLs are not used.

The AI prompt explicitly treats Moodle content as untrusted data so instructions embedded in course content are not
supposed to become tool instructions.

## Human review and WCAG limitations

This plugin is assistance, not certification. Passing the report does not prove WCAG conformance and the UI never labels
a course as “accessible” merely because no finding was returned.

A professibility review can require inspection of final rendering, keyboard behaviour, focus management, ARIA
relationships, screen-reader behaviour, media quality, colour use, authoring context, external players, linked
documents, PDFs and interactions that are outside the stored HTML analysed here.

No content is modified automatically. Every finding includes the source location and a Moodle-controlled edit link so
the teacher can decide what should change.

## Capability

The report requires:

`local/accessibilityai:audit`

By default it is granted to editing teachers and managers.

## Privacy

`local_accessibilityai` does not persist audit content or results in its own database. When AI review runs, minimized
teacher-authored course content is sent to `local_ai_bridge`, which applies its configured tenant, purpose, route,
provider, usage and privacy rules. The bridge may record its own usage metadata according to its implementation.

Student submissions are not analysed in this version.

## Tests

The PHPUnit suite contains HTML fixtures for every deterministic rule category requested, plus tests for:

- AI JSON parsing;
- rejection of unknown item IDs;
- sanitization of model output;
- malformed AI responses;
- capability defaults;
- false-positive guards for explicit decorative images and presentational tables.

## Continuous integration

`.github/workflows/ci.yml` installs the required AI bridge as an extra Moodle plugin, runs Moodle Plugin CI, PHPUnit
and `EduardoKrausME/moodle-plugin-validate`.

## License

GNU GPL v3 or later.
