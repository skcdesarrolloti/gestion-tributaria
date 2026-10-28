qpdf compiled to WebAssembly, @jspawn/qpdf-wasm 0.0.2 (Apache-2.0).
Source: https://github.com/jsscheller/qpdf-wasm
Distribution: https://registry.npmjs.org/@jspawn/qpdf-wasm/-/qpdf-wasm-0.0.2.tgz
Bundled without modifications. LICENSE accompanies the distribution.
qpdf.js SHA256: 0129e94930f34f9b3b371db00427f9ae81502bc8be4873f9ad114fc353ae306f
qpdf.wasm SHA256: cbd81a244d622a39e3cf16f2e589a9a0d7cc51f588d35b3dd6b425a677cc3f99
Only used in a browser Worker, with no outgoing requests beyond local engine
assets. Unlocks a user-supplied PDF with a known password; no key discovery.
Input/password are discarded when the worker terminates. Never log stderr.
