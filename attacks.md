# Web Form / API Attack-Surface Checklist
*For defensive threat modeling & security test prioritization — cross-referenced to OWASP Top 10:2025, OWASP API Security Top 10, OWASP LLM Top 10:2025, OWASP ASVS, CWE, and MITRE ATT&CK/CAPEC.*

**Applicability key:** **F** = HTML forms · **H** = HTTP/web requests · **A** = REST/GraphQL/gRPC APIs · **C** = CLI/scripted tools · **L** = AI/LLM-integrated inputs

---

## 1. Input Validation Attacks

| Technique | Reference | Vector (one line) | Surfaces |
|---|---|---|---|
| SQL Injection (in-band, blind, error-based, time-based) | CWE-89, OWASP A05:2025-Injection, CAPEC-66 | Untrusted input alters SQL query structure/semantics | F H A C |
| Second-order SQL Injection | CWE-89, CAPEC-66 | Malicious input stored safely first, then executed when retrieved and reused in a query | F H A |
| NoSQL Injection (Mongo `$where`/operator injection, etc.) | CWE-943 | Operator/JS injection into document-store queries | F H A C |
| OS Command Injection | CWE-78, CAPEC-88 | Input passed to shell/exec calls unsanitized | F H A C |
| Argument Injection | CWE-88, CAPEC-88 | Attacker injects command-line flags/options rather than full commands | F H A C |
| Shell Metacharacter Injection | CWE-78, CWE-88 | Injection of `;`, `\|`, `&&`, backticks into CLI-invoked processes | F H A C |
| LDAP Injection | CWE-90, CAPEC-136 | Input alters LDAP filter syntax | F H A |
| XML External Entity (XXE) Injection | CWE-611, CAPEC-228 | Malicious external entity refs in XML parsers (file read/SSRF) | F H A |
| XML Injection / XPath Injection | CWE-91, CWE-643, CAPEC-83 | Malformed XML/XPath alters document structure or query logic | F H A |
| Server-Side Template Injection (SSTI) | CWE-1336, WSTG-INPV-18, CAPEC-267 | Input evaluated by a server-side template engine (Jinja2, Twig, FreeMarker) | F H A |
| Client-Side Template Injection (CSTI) | CWE-1336, CAPEC-588 | Input evaluated by client-side template/JS framework sandbox (AngularJS `{{}}`) | F H |
| Expression Language Injection (EL/OGNL/SpEL) | CWE-917 | Input evaluated as an expression-language statement (e.g., Struts OGNL) | F H A |
| HTTP Header Injection | CWE-113, CAPEC-105 | Unsanitized input injected into response headers | H A |
| CRLF Injection / HTTP Response Splitting | CWE-93, CAPEC-105 | `\r\n` sequences forge extra headers or split responses | H A |
| Path Traversal / Directory Traversal | CWE-22, CAPEC-126 | `../` sequences escape intended file directory | F H A C |
| Local File Inclusion (LFI) | CWE-73, CWE-98 | App includes/executes attacker-chosen local file | F H A |
| Remote File Inclusion (RFI) | CWE-98 | App includes/executes attacker-hosted remote file | F H A |
| Log Injection / Log Forging | CWE-117 | Newline/control characters in input create false log entries or evade detection | F H A C |
| Email Header Injection (SMTP/IMAP) | CWE-93, CWE-150 | CRLF in form fields injects additional email headers for spam/phishing relay | F H A |
| CSV/Formula Injection | CWE-1236, CAPEC-506 | Cell content starting with `=`,`+`,`-`,`@` executes on spreadsheet open | F H A |
| Prototype Pollution (JS object merge) | CWE-1321 | Deep-merge of untrusted JSON pollutes object prototypes, altering logic or enabling RCE chains | F H A |
| Format String Injection | CWE-134 | User-controlled format specifiers in logging/printf-style functions cause memory read or crash | H A C |
| Server-Side JavaScript Injection | CWE-94 | Untrusted input evaluated by server-side JS engines (Node `eval`, VM) | F H A |
| HQL/JPQL/ORM Injection | CWE-89 | Injection into ORM query languages bypasses naive parameterization | F H A |
| GraphQL Variable Injection | OWASP API8:2023 | Unsanitized query variables passed to resolvers/backends cause downstream injection | A |
| gRPC Protobuf Field Injection | CWE-20, CWE-74 | Manipulated protobuf fields (including deprecated/`Any` types) bypass validation or trigger unsafe deserialization | A C |
| gRPC Metadata Injection | CWE-20, CWE-113 | Attacker-controlled metadata keys/values reach SQL, shell, or auth logic without validation | A C |
| Unicode/Homoglyph Normalization Bypass | CWE-176, ASVS V5.1 | Visually similar or differently normalized Unicode evades allowlist validation or auth checks | F H A |
| Type Juggling / Coercion Bypass | CWE-843 | Mixed-type comparisons (`"0" == false`, array vs string) bypass validation or auth logic | F H A |
| Integer Overflow/Underflow in Validation | CWE-190, CWE-191 | Extreme numeric values wrap or bypass bounds checks affecting pricing, IDs, or allocations | F H A |
| Deserialization of Untrusted Data | CWE-502, CAPEC-586 | Malicious serialized object triggers RCE/gadget chains on deserialize | H A |
| Regex Injection | CWE-1333 (adjacent) | User input becomes part of a compiled regex pattern | F H A |

## 2. Client-Side Attacks

| Technique | Reference | Vector | Surfaces |
|---|---|---|---|
| Stored XSS | CWE-79, OWASP A05:2025, CAPEC-63 | Malicious script persisted and served to other users | F H |
| Reflected XSS | CWE-79, CAPEC-63 | Script echoed back immediately from request parameters | F H |
| DOM-based XSS | CWE-79, CAPEC-588 | Client-side JS writes untrusted data into a dangerous DOM sink | F H |
| Mutation XSS (mXSS) | CWE-79 | Payload becomes malicious only after browser/sanitizer re-parses HTML | F H |
| Cross-Site Request Forgery (CSRF) | CWE-352, CAPEC-62 | Forged state-changing request riding victim's authenticated session | F H |
| Login CSRF | CWE-352 | Attacker forces victim to authenticate as attacker's account, capturing victim-entered data | F H |
| Clickjacking / UI Redressing | CWE-1021, CAPEC-587 | Transparent iframe tricks user into unintended clicks | F H |
| Cross-Frame Scripting (XFS) | CAPEC-587 | Framed victim page manipulated to leak data or perform actions via frame interaction | H |
| HTML Injection | CWE-79 (subset) | Injecting markup without script execution to deface/phish | F H |
| Open Redirect | CWE-601, CAPEC-593, ASVS V5.1.5 | Unvalidated redirect/forward parameter sends user to attacker site | F H A |
| Tabnabbing (reverse) | CWE-1022 | `target=_blank` without `rel=noopener` lets new tab rewrite opener | H |
| CORS Misconfiguration Abuse | CWE-942, OWASP A02:2025 | Overly permissive `Access-Control-Allow-Origin` lets attacker sites read authenticated API responses | H A |
| PostMessage / Cross-Origin Communication Abuse | CWE-346 | Missing origin checks on `window.postMessage` handlers | H |
| WebSocket XSS / Injection | CWE-79 | Unsanitized WebSocket messages rendered in DOM or passed to `eval`-like handlers | H A |
| CSS Injection / Data Exfiltration via CSS | CWE-79 (adjacent) | Attacker-controlled CSS selectors exfiltrate input values | F H |
| JSONP Callback Injection | CWE-79 | Untrusted callback parameter in JSONP responses executes arbitrary JavaScript | H A |
| Content Sniffing / MIME Confusion | CWE-430 | Browser interprets non-HTML upload/response as executable HTML/JS despite declared MIME type | F H A |
| Client-Side Storage Exposure | CWE-922 | Sensitive tokens/data stored in localStorage/sessionStorage accessible via XSS | F H |
| Form Action Hijacking | CWE-352 | `<form action>` pointed to attacker URL exfiltrates submitted credentials/data | F |
| Autofill Abuse | CWE-524 | Hidden fields or CSS tricks capture browser-autofilled credentials or PII | F |

## 3. Authentication / Session Attacks

| Technique | Reference | Vector | Surfaces |
|---|---|---|---|
| Credential Stuffing | CWE-307, CAPEC-600, OWASP API2/A07:2025 | Reuse of breached credential pairs at scale | F H A C |
| Brute Force / Password Spraying | CWE-307, CAPEC-49/CAPEC-565 | Automated guessing of passwords/usernames | F H A C |
| Session Fixation | CWE-384, CAPEC-61 | Attacker pre-sets a session ID victim later authenticates with | F H |
| Session Hijacking (token theft, sidejacking) | CWE-613, CAPEC-593 | Stolen/predicted session token reused by attacker | F H A |
| Insecure/Predictable Session Tokens | CWE-330 | Weak entropy allows session ID prediction | F H A |
| JWT Tampering (alg=none, key confusion, signature stripping) | CWE-347, CAPEC-459 | Forged/modified JWT accepted due to weak validation | H A |
| JWT Claim Tampering | CWE-287 | Modified `sub`, `role`, or `exp` claims accepted without proper signature validation | H A |
| JWT Secret Brute-Forcing / Weak Signing Key | CWE-326, CWE-798 | Guessable HMAC secret allows token forgery | H A C |
| OAuth/OIDC Misconfiguration Abuse (redirect_uri manipulation, implicit flow leakage, state/PKCE omission) | CWE-346, CAPEC-39 | Flawed OAuth flow allows token theft or account linking abuse | F H A |
| OAuth Scope Escalation | OWASP API2:2023 | Client requests or receives broader scopes than intended, gaining excess API access | A |
| OpenID Connect Mix-up Attack | CWE-287 | Confusion between multiple IdP responses causes token acceptance from wrong issuer | H A |
| Password Reset Poisoning (Host header injection into reset link) | CWE-640 | Manipulated Host header redirects reset link to attacker domain | F H A |
| Password Reset Token Brute Force | CWE-640 | Weak/predictable reset tokens guessed or enumerated to take over accounts | F H A |
| Insecure "Remember Me" / Persistent Auth Tokens | CWE-539, CWE-384 | Long-lived tokens with weak protection enable takeover | F H |
| Multi-Factor Authentication Bypass | CWE-308 | Flaws in MFA flow (response manipulation, missing step enforcement, push fatigue) | F H A |
| Account Enumeration | CWE-203, CWE-204 | Differing responses/timing reveal valid usernames/emails | F H A |
| Weak/Missing Account Lockout | CWE-307 | No throttling permits unlimited auth attempts | F H A |
| Bearer Token in URL Leakage | CWE-598 | Tokens passed in query strings leak via Referer logs, browser history, and proxies | F H A |
| API Key in Client/Repo Leakage | OWASP API2:2023, CWE-798, CWE-312 | Hardcoded or exposed API keys grant unauthorized API access at scale | A C |
| Session Cookie Attribute Weakness | ASVS V3, CWE-1275, CWE-1004 | Missing `Secure`/`HttpOnly`/`SameSite` enables theft or CSRF-based session abuse | F H |
| Concurrent Session Abuse | CWE-613 | Attacker maintains parallel sessions after victim password change due to missing invalidation | F H A |
| SAML/XML Signature Wrapping | CWE-347 | Manipulated SAML assertions bypass signature validation while appearing valid | H A |
| WebAuthn/Passkey Downgrade | OWASP A07:2025 | Fallback to weaker auth methods when passkey verification fails or is optional | F H |
| gRPC Metadata Auth Spoofing | OWASP API2:2023, CWE-287 | Backend trusts client-supplied `x-user-id`/`x-tenant-id` metadata without token verification | A C |

## 4. Authorization Attacks

| Technique | Reference | Vector | Surfaces |
|---|---|---|---|
| Insecure Direct Object Reference (IDOR) | CWE-639, OWASP API1:2023 BOLA | Direct access to objects via predictable/user-supplied IDs without ownership check | F H A |
| Broken Object-Level Authorization (BOLA) | OWASP API1:2023, CWE-639 | API returns/modifies objects belonging to other users | A |
| Broken Function-Level Authorization (BFLA) | OWASP API5:2023, CWE-285 | Low-privileged user reaches admin/privileged endpoints | F H A |
| Broken Object Property-Level Authorization (BOPLA) | OWASP API3:2023, CWE-915, CWE-213 | Excessive data exposure or unauthorized property write via API objects | A |
| Privilege Escalation (vertical/horizontal) | CWE-269, CAPEC-233 | User gains rights beyond intended role or another peer's data | F H A |
| Mass Assignment / Auto-Binding | CWE-915, OWASP API3:2023 | Extra unvalidated fields in request bind to sensitive model attributes (e.g., `isAdmin`) | F H A |
| Forced Browsing to Unlinked Resources | CWE-425, CAPEC-127 | Guessing/enumerating hidden endpoints not linked in UI | F H A C |
| Missing Function-Level Access Control | CWE-862 | Server fails to re-check authorization on sensitive actions | F H A |
| GraphQL Resolver Authorization Bypass | OWASP API1/API5:2023 | Field resolvers lack per-object checks; nested queries expose unauthorized related data | A |
| GraphQL Alias / Operation-Name Bypass | OWASP API5:2023 | Blocklists on operation names bypassed via aliases calling same sensitive fields | A |
| gRPC Method-Level Auth Bypass | OWASP API5:2023 | Some RPC methods missing interceptors while others are protected, enabling direct calls | A C |
| Multi-Tenant Isolation Failure | OWASP API1:2023, CWE-668 | Tenant ID not enforced; cross-tenant data read/write via shared infrastructure | A |
| Confused Deputy | CWE-441, OWASP A01:2025 | Application uses its elevated privileges on attacker's behalf to access internal resources | H A |
| Policy Bypass via HTTP Method Override | CWE-650 | `_method=DELETE` or `X-HTTP-Method-Override` bypasses method-restricted ACL rules | F H A |
| JWT Role Claim Trust Without Server Check | OWASP API5:2023, CWE-285 | Application trusts client-presented role claims without server-side RBAC enforcement | H A |
| File/Resource ID Guessing (UUID vs sequential) | OWASP API1:2023, CWE-330 | Predictable identifiers enable enumeration of unauthorized objects | F H A |
| Scope-Based OAuth Token Abuse | OWASP API5:2023 | Token valid for one resource used against broader API surface lacking scope checks | A |
| Attribute-Based Access Control (ABAC) Logic Flaws | CWE-284, OWASP A06:2025 | Complex policy rules contain edge cases allowing unintended access grants | F H A |

## 5. API-Specific Attacks

| Technique | Reference | Vector | Surfaces |
|---|---|---|---|
| Rate Limit Bypass (IP rotation, header spoofing, distributed) | OWASP API4:2023, CWE-799 | Circumventing throttling to enable brute force/scraping | H A C |
| Rate Limit Bypass (HTTP/2 multiplexing) | OWASP API4:2023, CWE-770 | Many parallel streams on one connection exceed per-connection limits | H A |
| Unrestricted Resource Consumption | OWASP API4:2023, CWE-770, CWE-400 | Missing limits on payload size, pagination, query depth, or execution time | H A C |
| Excessive Data Exposure | OWASP API3:2023, CWE-200 | API returns more fields than the client needs, relying on client-side filtering | A |
| Improper Inventory Management (shadow/zombie APIs) | OWASP API9:2023, CWE-1059 | Undocumented/deprecated API versions remain reachable and unmonitored | A C |
| Unsafe Consumption of Third-Party APIs | OWASP API10:2023, CWE-20, CWE-829 | Blind trust of data/responses from integrated external APIs | A |
| GraphQL Introspection Abuse | OWASP API8:2023, CWE-200 | Introspection query reveals full schema, aiding attacker recon | A C |
| GraphQL Batch Query / Aliasing Abuse | OWASP API4:2023, CAPEC-125 | Multiple aliased operations in one request bypass rate limits | A C |
| GraphQL Query Depth / Complexity (Nested Query DoS) | OWASP API4:2023, CWE-674, CWE-400 | Deeply nested or recursive queries exhaust server resources | A C |
| GraphQL Field Duplication / Alias Amplification | OWASP API4:2023 | Same expensive field requested many times via aliases multiplies backend load | A |
| GraphQL Mutation Abuse | OWASP API6:2023 | Unrestricted write mutations enable mass data modification without business-flow controls | A |
| GraphQL Subscription Abuse | OWASP API4:2023 | Long-lived subscriptions exhaust connections or stream sensitive events without auth | A |
| GraphQL Field Suggestion / Error-Based Enumeration | CWE-209 | Verbose error messages leak schema field names | A |
| REST Over-Fetching via Expand/Include | OWASP API3:2023 | `?include=` parameters pull related records beyond caller's authorization | A |
| REST Verb Tampering | CWE-650, OWASP A01:2025 | Using GET with body, POST for DELETE, or unsupported verbs hits unintended handlers | H A C |
| Content-Type Confusion | OWASP API8:2023, CWE-436 | Sending JSON as `text/plain` or duplicate Content-Type headers bypasses parser/WAF validation | H A C |
| gRPC Reflection Abuse | OWASP API9/API8:2023, CWE-200 | Server reflection service leaks proto/service definitions | A C |
| gRPC Message/Stream Flooding | OWASP API4:2023, CWE-400 | Abuse of streaming RPCs to exhaust connections/memory | A C |
| Protobuf Deprecated Field Abuse | OWASP API3:2023 | Deprecated but still-deserialized fields (e.g., `is_admin`) re-enabled by attackers | A |
| API Key / Secret Leakage (client-side exposure, repo leaks, logs) | CWE-798, CWE-532 | Hardcoded or logged API keys harvested by attacker | A C |
| API Versioning Abuse (older, less-secured version still live) | OWASP API9:2023 | Attacker targets deprecated endpoint with weaker controls | A |
| Improper Assets Management (staging/test endpoints exposed) | OWASP API9:2023 | Non-production environments reachable and less hardened | A C |
| Pagination Parameter Manipulation | OWASP API3:2023, CWE-20 | Extreme `limit`/`offset`/`page` values cause data dumps or resource exhaustion | A C |
| Filter/Sort Injection via API Params | OWASP A05:2025, CWE-89 | OData/JSON:API `$filter`, `sort`, or `where` params inject query logic | A C |
| HATEOAS Link Manipulation | OWASP A06:2025 | Client follows attacker-modified hypermedia links to unauthorized operations | A |
| Webhook Signature Bypass | OWASP API10:2023, CWE-345 | Missing/weak HMAC verification on inbound webhooks allows forged event injection | A |
| API Schema Bypass (additionalProperties) | OWASP API8:2023, CWE-20 | Undocumented JSON fields accepted by backend despite strict OpenAPI client contract | A C |
| Server-Side Request Forgery via Webhooks/Callbacks | OWASP API7:2023, CWE-918 | API-triggered outbound request abused to reach internal resources | A |

## 6. File Upload Attacks

| Technique | Reference | Vector | Surfaces |
|---|---|---|---|
| Malicious File Upload (webshell, executable) | CWE-434, CAPEC-1 | Uploading executable content that server later runs | F H A |
| MIME Type / Content-Type Spoofing | CWE-434 (adjacent) | Forged Content-Type/extension bypasses file-type filters | F H A |
| Double Extension / Null Byte Bypass | CWE-434 | `.php%00.jpg` or `.php.jpg` tricks weak extension checks | F H A |
| Zip Bomb / Decompression Bomb | CWE-409, CAPEC-231 | Highly compressed archive expands to exhaust disk/memory | F H A |
| Zip Slip (path traversal on archive extraction) | CWE-22 (archive variant) | Archive entry paths (`../../`) write outside extraction directory | F H A |
| Polyglot File Attacks (valid as two formats, e.g., GIF+JS, JPEG+PHP) | CWE-434 (adjacent) | File is simultaneously valid image and executable/script | F H A |
| Path Traversal via Filename | CWE-22, CWE-73 | `../../etc/passwd` in uploaded filename writes outside intended storage path | F H A |
| SVG Upload XSS | CWE-79, CWE-434 | SVG with embedded JavaScript executes when rendered inline in browser | F H A |
| XXE via Uploaded XML/SVG/Office Doc | CWE-611 | XML-based upload formats trigger external entity expansion during server-side parsing | F H A |
| Image/Metadata-Based Exploits (EXIF injection, ImageTragick-style parser RCE) | CWE-434, CAPEC-1 | Malformed image metadata or crafted image triggers parser vulnerability | F H A |
| Malicious Macro / Embedded Object in Documents | CAPEC-1 | Uploaded Office/PDF file contains macros or embedded scripts | F H A |
| Antivirus/Content-Scanner Evasion | CWE-434 (adjacent) | Obfuscation or encoding defeats upload-time malware scanning | F H A |
| Storage Bucket Misconfiguration (public ACL) | OWASP A02:2025, CWE-732 | Uploaded sensitive files stored in publicly listable/readable cloud buckets | H A |
| Content-Disposition Injection | CWE-113 | Manipulated download filename headers cause XSS or path issues on client save | H A |
| Metadata EXIF/IPTC Injection | CWE-74 | Embedded metadata in images processed/stored unsafely triggers downstream injection | F H A |
| Thumbnail/Preview Generation RCE | CWE-434 | Server-side preview pipeline executes embedded payloads in uploaded files | F H A |
| Unrestricted File Size (storage exhaustion) | CWE-770, OWASP API4:2023 | No cap on upload size enables disk exhaustion DoS | F H A C |
| LLM Document Poisoning via Upload | OWASP LLM04:2025, CWE-345 | Uploaded files ingested into RAG/embedding pipeline inject malicious instructions | F H A L |

## 7. Business Logic Attacks

| Technique | Reference | Vector | Surfaces |
|---|---|---|---|
| Race Conditions (TOCTOU) | CWE-362, CWE-367, CAPEC-26 | Concurrent requests exploit timing gap (e.g., double-spend coupon/balance) | F H A C |
| Workflow/Process Bypass (skipping required steps) | CWE-841, OWASP API6:2023 | Directly calling a later-stage endpoint skips validation steps | F H A |
| Price/Quantity/Parameter Manipulation | CWE-472, CWE-840, CAPEC-93 | Client-controlled price, discount, or quantity fields tampered | F H A |
| Inventory/Coupon Abuse (reuse, stacking) | CWE-841 (adjacent), OWASP API6:2023 | Single-use business rule bypassed via replay or parallel requests | F H A |
| Currency/Rounding Abuse | CWE-682, OWASP A06:2025 | Floating-point or rounding errors exploited for micro-refunds or free purchases | F H A |
| Referral/Reward Farming | OWASP API6:2023 | Self-referrals or synthetic accounts harvest signup bonuses at scale | F H A C |
| State Machine Violation | OWASP A06:2025 | Invalid status transitions (cancel after ship, approve own request) accepted by API | F H A |
| Replay Attack (non-idempotent ops) | CWE-294, OWASP A06:2025 | Valid signed request replayed to duplicate payment, vote, or transfer | H A |
| Time-of-Check Time-of-Use (Inventory) | CWE-367 | Two checkout requests pass stock check simultaneously, overselling inventory | F H A |
| Insufficient Anti-Automation on Business Transactions | CWE-799, OWASP API6:2023 | No limits on repeated sensitive transactions (e.g., unlimited free trials) | F H A C |
| Negative Value / Integer Boundary Abuse | CWE-1284, CWE-190 | Negative or overflow values in quantity/amount fields break logic | F H A |
| Feature Flag / Beta Bypass | OWASP A01:2025 | Hidden parameters or endpoints enable unreleased/paid features without entitlement | F H A |
| Multi-Step Form Partial Submission | OWASP A06:2025 | Required validation on step 3 bypassed by POSTing final step directly | F H A |
| Captcha/Verification Step Bypass | OWASP API6:2023 | Bot submits form/API call skipping human-verification step in sequence | F H A C |
| Approval Chain Bypass | OWASP API6:2023 | Sensitive action executed without required approver by manipulating request order | H A |
| Subscription/Trial Reset Abuse | OWASP API6:2023 | New accounts or device IDs repeatedly claim free trials | F H A C |
| Function Chaining Abuse (using legitimate features out of intended sequence) | CAPEC-122 | Combining valid API calls in unintended order for advantage | H A |
| GraphQL Business Logic via Nested Mutations | OWASP API6:2023 | Chained mutations in one query bypass per-step business validations | A |
| gRPC Zero-Value Field Bypass | OWASP A06:2025 | Omitting proto3 fields sends zero defaults that skip non-zero validation checks | A C |
| LLM-Assisted Decision Bypass | OWASP LLM06:2025, CWE-284 | Prompt manipulation causes AI workflow to approve/deny transactions incorrectly | L |

## 8. Infrastructure / Protocol Attacks

| Technique | Reference | Vector | Surfaces |
|---|---|---|---|
| Server-Side Request Forgery (SSRF) | CWE-918, OWASP A01:2025, API7:2023, CAPEC-664 | Server induced to make requests to internal/attacker-chosen targets | F H A |
| Blind SSRF | CWE-918 | SSRF confirmed via out-of-band interaction, no direct response leak | F H A |
| DNS Rebinding | CAPEC-275, CWE-350 | DNS TTL manipulation changes resolved IP after security check, targeting internal hosts | H A |
| HTTP Request Smuggling (CL.TE, TE.CL, TE.TE) | CWE-444, CAPEC-33 | Discrepancy between front-end/back-end parsing of request boundaries | H A |
| HTTP Response Smuggling | CWE-444, CAPEC-273 | Parser discrepancies cause malicious responses to affect downstream clients/caches | H A |
| HTTP Response Splitting | CWE-113, CAPEC-105 | Injected CRLF forges additional HTTP responses | H A |
| HTTP Parameter Pollution (HPP) | CWE-235, CAPEC-460, ASVS V5.1.1 | Duplicate parameter names processed inconsistently by different layers | F H A C |
| Web Cache Poisoning | CAPEC-141, CWE-444 (adjacent) | Unkeyed input cached and served to other users | H A |
| Cache Deception | CWE-524 | Tricking cache into storing private/dynamic content as public | H |
| Host Header Injection | CWE-644, CAPEC-272 | Manipulated Host header abused for routing, cache, or reset-link attacks | F H A |
| HTTP/2 Rapid Reset / Continuation Flood | CWE-400 | Protocol-level floods exhaust server resources via stream manipulation | H A |
| HPACK Bomb (HTTP/2 header compression) | CWE-409 | Compressed headers expand massively, causing memory exhaustion on HTTP/2/gRPC paths | H A |
| TLS/SSL Downgrade & Stripping | CWE-319, CAPEC-220 | Forcing plaintext or weak-cipher connection | H A |
| Man-in-the-Middle via Certificate Validation Bypass | CWE-295 | App fails to validate TLS cert, enabling interception | H A |
| Reverse Proxy Misrouting | OWASP A02:2025, CWE-441 | Path normalization differences route requests to unintended internal backends | H A |
| IP Allowlist Bypass (X-Forwarded-For) | CWE-290, OWASP A02:2025 | Spoofed forwarding headers trick app into treating request as internal/trusted | H A C |
| WebSocket Origin Bypass | CWE-346 | Missing Origin check on WebSocket upgrade allows cross-site connection hijacking | H A |
| Server-Side Include (SSI) Injection | CWE-97, OWASP A05:2025 | User input in `.shtml` or SSI-enabled templates executes server directives | F H A |
| Deserialization Attack (infrastructure context) | OWASP A08:2025, CWE-502 | Untrusted serialized objects (Java, PHP, .NET) trigger gadget chain RCE on reload | H A |
| Insecure Direct Cloud Metadata Access | CWE-918 | SSRF or misconfig exposes IAM credentials from instance metadata service | H A |
| Subdomain Takeover | CWE-350 (adjacent) | Dangling DNS record pointed at decommissioned service claimed by attacker | H |
| DNS Cache Poisoning | CAPEC-142 | Forged DNS responses redirect traffic | H |
| Email/SMS Webhook SSRF | OWASP API7:2023, CWE-918 | Inbound webhook fetchers pull attacker URLs, reaching internal services | A |

## 9. Denial of Service

| Technique | Reference | Vector | Surfaces |
|---|---|---|---|
| Application-Layer DoS (logic bombs) | OWASP API4:2023, CWE-400 | Expensive code paths (reports, search, graph traversal) invoked repeatedly to exhaust CPU | F H A C |
| Slowloris / Slow POST | CWE-400, CAPEC-469, CAPEC-124 | Slow/partial requests exhaust connection pools | H A C |
| Regular Expression DoS (ReDoS) | CWE-1333, CAPEC-492 | Catastrophic backtracking regex triggered by crafted input | F H A |
| Resource Exhaustion (CPU, memory, disk, threads) | CWE-400, CWE-770, CAPEC-130 | Unbounded processing consumes finite server resources | F H A C |
| Large Payload / Body Bomb | CWE-770, ASVS V5.1 | Oversized JSON/XML/file bodies exceed limits and exhaust parsers or buffers | F H A C |
| Billion Laughs (XML entity expansion) | CWE-776, CAPEC-197 | Recursive entity expansion in XML parser consumes exponential memory/CPU | F H A |
| JSON Deep Nesting DoS | CWE-400 | Deeply nested JSON objects/arrays exhaust parser stack during deserialization | H A C |
| Algorithmic Complexity Attacks (hash collision, worst-case data structures) | CWE-407 | Crafted input drives algorithm to worst-case time complexity | F H A |
| Database Connection Pool Exhaustion | CWE-770 | Concurrent long-running queries hold all DB connections, blocking legitimate traffic | H A |
| Thread/Worker Pool Exhaustion | CWE-770 | Blocking sync calls or thread-per-request models exhausted under parallel load | H A |
| GraphQL Query Cost Amplification | OWASP API4:2023, CWE-400 | Single query triggers millions of DB lookups via nested list fields | A C |
| gRPC Streaming Flood | OWASP API4:2023, CWE-400 | Client opens unlimited bidirectional streams without backpressure or limits | A C |
| Account Lockout DoS | CWE-770 | Attacker triggers lockout of victim accounts via repeated failed login attempts | F H A |
| Email/SMS Flooding (OTP spam) | OWASP API4:2023, CWE-799 | Unthrottled notification endpoints spam victims or exhaust SMS/email quotas | F H A C |
| Amplification/Reflection Abuse (via API to third party) | CAPEC-490 | API used as a proxy to flood a third target | H A |
| Distributed Denial of Service (volumetric, via botnets) | CAPEC-125 | Coordinated high-volume traffic overwhelms infrastructure | H A |
| Unbounded Consumption (LLM token/context exhaustion) | OWASP LLM10:2025, CWE-400, CWE-770 | Oversized prompts or recursive agent loops exhaust model quota and budget | L |
| LLM Inference Cost Amplification | OWASP LLM10:2025, CWE-770 | Adversarial inputs force expensive model reasoning or tool-call loops | L |

## 10. Bot / Automation Attacks

| Technique | Reference | Vector | Surfaces |
|---|---|---|---|
| Credential Scraping / Stuffing at Scale | CAPEC-600, OWASP API2:2023 | Automated login attempts using leaked credential lists | F H A C |
| Content Scraping | CWE-799 (adjacent), CAPEC-118 | Automated harvesting of proprietary site content | H A C |
| Form Spam Submission | CWE-799, OWASP API6:2023 | Automated bulk submission of forms (comments, signups) | F H A |
| CAPTCHA Bypass (OCR solving, audio solving, farm services) | CAPEC-141 (adjacent), OWASP API6:2023 | Automated or outsourced human solving defeats CAPTCHA | F H A |
| Headless Browser / Automation Framework Abuse (Puppeteer, Selenium, Playwright) | CWE-799 (adjacent), OWASP API6:2023 | Full browser automation mimics legitimate user to evade detection | F H A C |
| Fingerprint/Device Spoofing | CAPEC-151 (adjacent), OWASP API8:2023 | Forged browser/device signals evade bot-detection heuristics | H A C |
| Card/Gift-Card Cracking (BIN attacks) | CAPEC-112 (adjacent) | Automated testing of card number ranges via payment forms | F H |
| Inventory/Ticket Hoarding (scalping bots) | CWE-799 (adjacent), OWASP API6:2023 | Automated bulk purchase/reservation bypassing fair-use limits | F H A C |
| Ticket/Queue Bypass Bots | OWASP API6:2023 | Speed advantage in limited-release flows (SNKRS, concert tickets) | F H A C |
| Click Fraud | CAPEC-153 (adjacent) | Automated fraudulent clicks on ad/referral endpoints | F H |
| API Enumeration Bots | OWASP API9:2023 | Automated discovery of endpoints, parameters, and object ID ranges | A C |
| Honeypot Field Bypass | OWASP API6:2023 | Bots fill hidden form fields meant to trap non-human submitters | F |
| Behavioral Biometric Evasion | OWASP API6:2023 | Synthetic mouse/keyboard timing patterns mimic human interaction | F H |
| LLM-Powered Form Filling Bots | OWASP LLM06:2025 | AI agents auto-complete complex multi-step forms with contextual plausibility | F H L |

## 11. CLI / Script-Based Attack Tooling

| Technique | Reference | Vector | Surfaces |
|---|---|---|---|
| Automated Fuzzing (ffuf, wfuzz, gobuster - endpoint/parameter discovery) | CAPEC-215, CAPEC-310 | Wordlist-driven brute discovery of hidden routes/params | H A C |
| Automated Injection Scanning (sqlmap, NoSQLMap) | CAPEC-66, OWASP A05:2025 | Tool-driven systematic injection point discovery and exploitation | F H A C |
| Intercepting Proxy Abuse (Burp Suite, OWASP ZAP, mitmproxy) | CAPEC-94 (adjacent) | Manual/automated request tampering via proxy interception | F H A C |
| Scripted curl/wget/httpie Abuse (mass requests, header forgery) | CWE-799 | Command-line HTTP clients scripted for scanning or flooding | H A C |
| Shell Metacharacter Injection into CLI Wrappers | CWE-78, CWE-88 | CLI tool passes unsanitized args to underlying shell | C |
| Environment Variable Injection | CWE-426, CWE-78 | Untrusted input sets `PATH`, `LD_PRELOAD`, or config env vars affecting subprocess behavior | C |
| Path Traversal in File Arguments | CWE-22 | CLI accepts `-f ../../etc/passwd` without path canonicalization | C |
| API Fuzzing Frameworks (Postman/Newman scripts, RESTler, Schemathesis) | CAPEC-215, OWASP API8:2023 | Schema-driven automated malformed-request generation | A C |
| gRPC Fuzzing (grpcurl + custom payloads) | OWASP API8:2023 | Binary protobuf fuzzing via reflection discovers parser crashes and auth gaps | A C |
| GraphQL Introspection + Automated Query Gen | OWASP API8:2023 | Tools auto-generate queries from schema to map data exfil paths | A C |
| Directory/Parameter Brute Forcing (dirb, dirsearch) | CAPEC-127 | Wordlist enumeration of paths and query parameters | H A C |
| Credential Brute-Force Tools (Hydra, Medusa) | CAPEC-49 | Automated multi-protocol credential guessing | F H A C |
| Network/Service Recon (nmap scripting engine) | CAPEC-300 (recon family) | Automated port/service fingerprinting ahead of targeted attack | H A C |
| CI/CD Pipeline Script Injection (malicious build scripts abusing CLI tools) | CWE-78 (adjacent) | Untrusted input reaches build/deploy shell scripts | C |
| YAML/Config Injection (CI scripts) | CWE-94, CWE-502 | Crafted config files processed by CLI tools execute unintended directives | C |
| Token/Cookie Harvest via Script Replay | CWE-798 | Stolen session artifacts replayed at scale from attacker-controlled scripts | H A C |
| Rate-Limit Evasion via Distributed Scripts | OWASP API4:2023, CWE-799 | Botnet or cloud functions rotate IPs/keys to stay under per-IP thresholds | H A C |
| LLM Red-Team Automation (Garak, Promptfoo) | OWASP LLM01:2025 | Automated prompt suites systematically probe jailbreaks and injection in AI endpoints | L C |

## 12. AI / LLM-Specific Attacks

*(applies when form/API input feeds an LLM, agent, or RAG pipeline)*

| Technique | Reference | Vector | Surfaces |
|---|---|---|---|
| Direct Prompt Injection | OWASP LLM01:2025, CWE-1427, CWE-20, CWE-74 | User input directly overrides system instructions | F H A L |
| Indirect Prompt Injection | OWASP LLM01:2025, CWE-1427 | Malicious instructions embedded in retrieved/external content (web page, doc, email) the LLM ingests | H A L |
| Jailbreaking (persona, roleplay, DAN-style, obfuscation/encoding bypass) | OWASP LLM01:2025 (subset) | Techniques that get the model to ignore safety constraints | L |
| Many-Shot / Context Overflow Jailbreak | OWASP LLM01:2025, CWE-770 | Long context filled with examples gradually erodes model adherence to restrictions | L |
| Adversarial Suffix / Token Smuggling | OWASP LLM01:2025, CWE-74 | Machine-optimized suffix tokens bypass filters while remaining effective against the model | L |
| System Prompt Leakage | OWASP LLM07:2025, CWE-200, CWE-215 | Attacker extracts confidential system/developer instructions | L |
| Sensitive Information Disclosure | OWASP LLM02:2025, CWE-200, CWE-359 | Model reveals PII, secrets, or training/context data in output | F H A L |
| PII Exfiltration via Form → LLM Pipeline | OWASP LLM02:2025, CWE-359 | User-submitted form data in shared context leaked to subsequent users or logs | F H L |
| Insecure Output Handling / Improper Output Handling | OWASP LLM05:2025, CWE-79, CWE-89, CWE-78 | Downstream system trusts LLM output without sanitization (leads to XSS, SSRF, injection) | H A L |
| AI-Generated Content XSS (stored via LLM output) | OWASP LLM05:2025, CWE-79 | LLM produces markdown/HTML with script that app renders unsafely to other users | F H L |
| Training Data / Data & Model Poisoning | OWASP LLM04:2025, CWE-345, CWE-20 | Malicious data introduced into fine-tuning/training or RAG index | L |
| RAG Corpus Poisoning | OWASP LLM04:2025, CWE-345 | Attacker publishes SEO-optimized pages/documents ingested into retrieval index with malicious instructions | H A L |
| Model Extraction / Model Theft | OWASP LLM03 (adjacent), CAPEC-ext, CWE-200 | Repeated querying used to approximate or steal proprietary model behavior/weights | A L |
| Model Inversion / Membership Inference | OWASP LLM02:2025 (adjacent) | Inferring training data or membership via crafted queries | L |
| Excessive Agency | OWASP LLM06:2025, CWE-269, CWE-284 | LLM agent granted more tool/permission scope than task requires, enabling unintended actions | L |
| Tool/Function-Calling Abuse | OWASP LLM06:2025, CWE-77 (adjacent) | Crafted input manipulates which tool/function the agent invokes or its arguments | A L |
| Vector/Embedding Store Weaknesses (RAG poisoning, embedding inversion) | OWASP LLM08:2025, CWE-345, CWE-327 | Malicious documents injected into retrieval index skew responses | A L |
| Retrieval Cross-Tenant Leakage | OWASP LLM02:2025, CWE-668 | Weak vector DB ACLs return other tenants' embedded documents in RAG context | A L |
| Supply Chain (malicious models, plugins, LoRA adapters, datasets) | OWASP LLM03:2025, CWE-494, CWE-829 | Compromised third-party model/plugin artifact introduces backdoor | L |
| Misinformation / Hallucination Exploitation | OWASP LLM09:2025, CWE-1021 | Attacker leverages model's confident false output for downstream harm | L |
| Unbounded Consumption (resource/cost DoS via prompts) | OWASP LLM10:2025, CWE-400, CWE-770 | Crafted prompts drive excessive token generation/compute cost | A L |
| Multi-Modal Injection (malicious instructions hidden in uploaded images/audio/PDF) | OWASP LLM01:2025 (indirect variant) | Payload embedded in non-text upload processed by multimodal model | F H L |
| Prompt Log Injection | CWE-117, OWASP LLM02:2025 | Newlines/instructions in form fields poison prompt logs read by admins or downstream systems | F H L |

## 13. Supply Chain / Dependency Attacks

| Technique | Reference | Vector | Surfaces |
|---|---|---|---|
| Vulnerable/Outdated Third-Party Libraries | OWASP A03:2025, CWE-1104, CWE-1035 | Known-CVE dependency exploited before patching | F H A C L |
| Malicious Package Injection (typosquatting, dependency confusion) | CWE-1357, CWE-494, CAPEC-538 | Attacker-published package mimics or shadows a legitimate internal/public package name | C |
| Compromised Build Pipeline / CI Injection | CWE-1357, CWE-506 | Malicious code inserted during build via compromised CI runner or script | C |
| Malicious/Compromised CDN or Third-Party Script | CWE-829, OWASP A08:2025 | Externally hosted JS/CSS asset altered to serve malicious payload | F H |
| Third-Party Form Widget Compromise | OWASP A06:2025, CWE-829 | Embedded chat, analytics, or CAPTCHA script turned malicious affects all form pages | F H |
| Software/Data Integrity Failures (unsigned updates, insecure deserialization pipelines) | OWASP A08:2025, CWE-502 | Missing integrity checks let tampered artifacts be trusted | H A C |
| Compromised Signing Keys / Certificate Abuse | CWE-347 (adjacent) | Stolen code-signing credentials used to trust malicious releases | C |
| Vendor/Third-Party API Compromise | OWASP API10:2023, CWE-829 | Trusted upstream API integration becomes an attack conduit | A |
| Container/Base Image Supply Chain Risk | CWE-1104 (adjacent) | Compromised base image or IaC template ships vulnerabilities into production | C |
| LLM Model/Adapter Supply Chain | OWASP LLM03:2025, CWE-494 | Backdoored LoRA adapter, prompt template lib, or model checkpoint from untrusted source | L |
| Protobuf/gRPC Library Deserialization CVE | OWASP A06:2025, CWE-502 | Parser bugs (e.g., protobuf-java CVE-2022-3171) crash or DoS service via crafted messages | A C |
| Transitive Dependency Blind Spot | OWASP A06:2025, CWE-1104 | Vulnerability buried in nested dependency not tracked by SBOM or scanner | C |
| Secrets in Client Bundles / Mobile Apps | OWASP A04:2025, CWE-798 | API keys and endpoints exposed in frontend JS or APK enable direct API abuse | H A C |
| API Gateway / WAF Rule Bypass via CVE | OWASP A06:2025, CWE-1104 | Known vulnerability in edge security product exposes backend form/API directly | H A |

---

## 14. Cross-Cutting Test Matrix (by surface)

| Category | HTML Forms | HTTP Requests | REST API | GraphQL | gRPC | CLI/Scripts | LLM Pipeline |
|----------|:----------:|:-------------:|:--------:|:-------:|:----:|:-----------:|:------------:|
| Input Validation | ● | ● | ● | ● | ● | ● | ● |
| Client-Side | ● | ● | ○ | ○ | ○ | ○ | ○ |
| Auth/Session | ● | ● | ● | ● | ● | ○ | ● |
| Authorization | ● | ● | ● | ● | ● | ○ | ● |
| API-Specific | ○ | ○ | ● | ● | ● | ● | ○ |
| File Upload | ● | ● | ● | ○ | ○ | ○ | ● |
| Business Logic | ● | ● | ● | ● | ● | ○ | ● |
| Infrastructure | ○ | ● | ● | ● | ● | ○ | ● |
| DoS | ● | ● | ● | ● | ● | ● | ● |
| Bot/Automation | ● | ● | ● | ● | ○ | ● | ● |
| CLI/Script | ○ | ● | ● | ● | ● | ● | ● |
| AI/LLM | ○ | ○ | ● | ● | ○ | ● | ● |
| Supply Chain | ● | ● | ● | ● | ● | ● | ● |

● = primary surface · ○ = secondary / indirect

---

## 15. Recommended Testing Order (P1 → P3)

**Phase 1 — Auth & access (same day)**
- IDOR/BOLA on every object ID (§4)
- BFLA on admin endpoints (§4)
- Mass assignment on form/API bodies (§4)
- CSRF on state-changing forms (§2)
- Broken auth / JWT issues (§3)

**Phase 2 — Injection & SSRF (day 1–2)**
- SQLi/NoSQLi on all inputs (§1)
- Command/path/template injection (§1)
- SSRF on URL/webhook/fetch fields (§8)
- XSS stored/reflected on all outputs (§2)
- File upload bypass (§6)

**Phase 3 — API/protocol depth (day 2–3)**
- GraphQL introspection, batching, depth limits (§5)
- gRPC reflection + metadata spoofing (§5, §3)
- Rate limit bypass (§5)
- HTTP smuggling/HPP if behind proxy (§8)
- Business logic races and workflow bypass (§7)

**Phase 4 — Automation, DoS, supply chain (ongoing)**
- Bot abuse and credential stuffing (§10)
- ReDoS and large payload limits (§9)
- Dependency/SBOM scan (§13)
- LLM prompt injection if AI-integrated (§12)

---

## Cross-Reference Quick Map

- **OWASP Top 10:2025 (web app):** A01 Broken Access Control (incl. SSRF, BOLA/BFLA) · A02 Security Misconfiguration · A03 Software Supply Chain Failures · A04 Cryptographic Failures · A05 Injection · A06 Insecure Design · A07 Identification & Authentication Failures · A08 Software & Data Integrity Failures · A09 Security Logging & Alerting Failures · A10 Mishandling of Exceptional Conditions.
- **OWASP API Security Top 10 (2023):** API1 BOLA · API2 Broken Authentication · API3 BOPLA (excessive data exposure/mass assignment) · API4 Unrestricted Resource Consumption · API5 BFLA · API6 Unrestricted Access to Sensitive Business Flows · API7 SSRF · API8 Security Misconfiguration · API9 Improper Inventory Management · API10 Unsafe Consumption of APIs.
- **OWASP Top 10 for LLM Applications (2025 v2.0):** LLM01 Prompt Injection · LLM02 Sensitive Information Disclosure · LLM03 Supply Chain · LLM04 Data & Model Poisoning · LLM05 Improper Output Handling · LLM06 Excessive Agency · LLM07 System Prompt Leakage · LLM08 Vector & Embedding Weaknesses · LLM09 Misinformation · LLM10 Unbounded Consumption.
- **OWASP ASVS:** use as the control-verification layer (V1 Architecture, V2 Auth, V3 Session, V4 Access Control, V5 Validation/Encoding, V8 Data Protection, V9 Communications, V12 File/Resources, V13 API/Web Service, V14 Config) to map each attack row above to a testable requirement.
- **MITRE ATT&CK:** map web/API findings to Initial Access (T1190 Exploit Public-Facing Application), Credential Access (T1110 Brute Force, T1552 Unsecured Credentials), and Resource Development/C2 techniques as relevant for adversary-emulation exercises.
- **MITRE ATLAS:** use for AI/ML-specific adversary tactics (AML.T0051 LLM Prompt Injection, AML.T0024 Exfiltration via Model Inference, etc.) alongside the LLM Top 10 rows above.
- **Key CAPEC patterns cited:** CAPEC-33 (HTTP Smuggling) · CAPEC-62 (CSRF) · CAPEC-63 (XSS) · CAPEC-66 (SQLi) · CAPEC-88 (Command Injection) · CAPEC-105 (HTTP Response Splitting) · CAPEC-126 (Path Traversal) · CAPEC-141 (Cache Poisoning) · CAPEC-228 (XXE) · CAPEC-275 (DNS Rebinding) · CAPEC-587 (Clickjacking) · CAPEC-588 (DOM XSS) · CAPEC-664 (SSRF)

---

## How to Use This for Prioritization

1. Map each row to the actual data flow in your architecture diagram (which components touch which category).
2. Score by exploitability × business impact, not by list order — e.g., BOLA on a financial object outranks a low-severity open redirect.
3. Pair each row with an ASVS control ID as your "definition of done" for the fix.
4. For the AI/LLM section, treat indirect prompt injection and excessive agency as highest priority if the system has tool-calling/agentic capability — these have the widest blast radius.
5. Re-run this checklist whenever a new data source, third-party integration, or LLM tool is added — supply chain and inventory-management gaps (§5, §13) are the most common source of "unknown unknowns."

**Total checklist items: 200+ named techniques across 13 categories.**
