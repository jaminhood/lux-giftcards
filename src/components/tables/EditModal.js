import { motion } from "framer-motion"

const EditModal = ({ toggleOpen, modalTitle, children, submit }) => {
	const handleSubmit = () => {
		submit()
		toggleOpen()
	}

	return (
		<motion.div
			className="fixed inset-0 z-50 flex items-center justify-center w-screen h-screen"
			initial={{ opacity: 0 }}
			animate={{ opacity: 1 }}
			exit={{ opacity: 0 }}>
			<div
				className="absolute inset-0 opacity-50 bg-main-primary -z-10 blur-sm"
				onClick={toggleOpen}></div>
			<div className="relative w-full max-w-3xl p-8 rounded-md shadow-md bg-zinc-50">
				<div className="flex items-center justify-between pb-2 border-b border-gray-300">
					<h4 className="text-xl font-bold">{modalTitle}</h4>
				</div>
				<div className="py-4 overflow-hidden overflow-y-auto max-h-[24rem]">{children}</div>
				<div className="flex items-center justify-between pt-2 border-t border-gray-300">
					<button
						type="button"
						onClick={toggleOpen}
						className="px-[2rem!important] py-[.5rem!important] transition-all duration-300 ease-in-out bg-green-100 border rounded-md cursor-pointer text-main-primary border-main-primary hover:bg-main-primary hover:text-green-100 focus:outline-none">
						Close
					</button>
					<button
						type="button"
						onClick={handleSubmit}
						className="px-[2rem!important] py-[.5rem!important] transition-all duration-300 ease-in-out bg-green-100 border rounded-md cursor-pointer text-main-primary border-main-primary hover:bg-main-primary hover:text-green-100 focus:outline-none">
						Submit
					</button>
				</div>
			</div>
		</motion.div>
	)
}

export default EditModal
