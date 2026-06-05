const { createBlock: e, createElementVNode: t, createTextVNode: n, createVNode: r, openBlock: i, ref: a, resolveComponent: o, toDisplayString: s, withCtx: c } = window.__invoiceshelf_vue;
//#region resources/js/pages/DashboardPage.vue
var l = { class: "p-6" }, u = { class: "flex items-center gap-3" }, d = { class: "text-xl font-semibold text-heading" }, f = { class: "mt-6 rounded-lg bg-surface-secondary p-4" }, p = { class: "space-y-1.5 text-sm text-muted" }, m = { class: "flex items-start gap-2" }, h = { class: "flex items-start gap-2" }, g = { class: "flex items-start gap-2" }, _ = {
	__name: "DashboardPage",
	setup(_) {
		let v = a("Hello, world!");
		return (a, _) => {
			let y = o("BaseBreadcrumbItem"), b = o("BaseBreadcrumb"), x = o("BasePageHeader"), S = o("BaseIcon"), C = o("BaseCard"), w = o("BasePage");
			return i(), e(w, null, {
				default: c(() => [r(x, { title: "Hello World" }, {
					default: c(() => [r(b, null, {
						default: c(() => [r(y, {
							title: "Home",
							to: "dashboard"
						}), r(y, {
							title: "Hello World",
							to: "#",
							active: ""
						})]),
						_: 1
					})]),
					_: 1
				}), r(C, { class: "mt-6" }, {
					default: c(() => [t("div", l, [
						t("div", u, [r(S, {
							name: "HandRaisedIcon",
							class: "h-8 w-8 text-primary-500"
						}), t("h2", d, s(v.value), 1)]),
						_[12] ||= t("p", { class: "mt-3 text-sm text-muted leading-relaxed" }, [
							n(" This page is provided by the "),
							t("strong", null, "HelloWorld"),
							n(" module. It demonstrates how modules can ship their own Vue pages that render inside the InvoiceShelf SPA using globally registered Base components. ")
						], -1),
						t("div", f, [_[11] ||= t("h3", { class: "text-sm font-semibold text-heading mb-2" }, "How it works", -1), t("ul", p, [
							t("li", m, [
								r(S, {
									name: "CheckIcon",
									class: "h-4 w-4 text-green-500 shrink-0 mt-0.5"
								}),
								_[0] ||= n(" Module ships a compiled ", -1),
								_[1] ||= t("code", { class: "text-primary-600" }, "init.js", -1),
								_[2] ||= n(" that calls ", -1),
								_[3] ||= t("code", { class: "text-primary-600" }, "window.InvoiceShelf.booting()", -1)
							]),
							t("li", h, [
								r(S, {
									name: "CheckIcon",
									class: "h-4 w-4 text-green-500 shrink-0 mt-0.5"
								}),
								_[4] ||= n(" The callback receives ", -1),
								_[5] ||= t("code", { class: "text-primary-600" }, "(app, router)", -1),
								_[6] ||= n(" and adds routes via ", -1),
								_[7] ||= t("code", { class: "text-primary-600" }, "router.addRoute()", -1)
							]),
							t("li", g, [
								r(S, {
									name: "CheckIcon",
									class: "h-4 w-4 text-green-500 shrink-0 mt-0.5"
								}),
								_[8] ||= n(" Vue pages use globally registered ", -1),
								_[9] ||= t("code", { class: "text-primary-600" }, "Base*", -1),
								_[10] ||= n(" components — no imports needed ", -1)
							])
						])])
					])]),
					_: 1
				})]),
				_: 1
			});
		};
	}
};
//#endregion
//#region resources/js/init.ts
window.InvoiceShelf.booting((e, t) => {
	t.addRoute("admin", {
		path: "modules/hello-world/dashboard",
		name: "modules.hello-world.dashboard",
		component: _,
		meta: { requiresAuth: !0 }
	});
});
//#endregion
