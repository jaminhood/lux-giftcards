import { useEffect, useState } from "react"
import { ImPencil2 } from "react-icons/im"
import { pastDate } from "../../helpers"

const HomeTableData = ({ setModalIsOpen, data }) => {
	const { asset_img, asset_name, customer_name, price, order_status, proof, quantity, time_stamp } = data
	const [cls, setCls] = useState(`border-l-green-500`)

	useEffect(() => {
		switch (parseInt(order_status)) {
			case 0:
				setCls(`border-l-red-500`)
				break
			case 1:
				setCls(`border-l-yellow-500`)
				break
			case 2:
				setCls(`border-l-green-500`)
				break
		}
	}, [data])

	const handleModal = () => setModalIsOpen(data)

	return (
		<tbody>
			<tr className={`border-b border-b-zinc-300 border-l-8 ${cls} text-zinc-700`}>
				<td className="p-2">{customer_name}</td>
				<td className="py-2">{pastDate(new Date(time_stamp).toISOString())}</td>
				<td className="py-2">{asset_name}</td>
				<td className="p-2">
					<img
						src={asset_img}
						alt={asset_name}
						className="w-12"
					/>
				</td>
				<td className="p-2">{parseInt(quantity)}</td>
				<td className="p-2">{parseFloat(price)}</td>
				<td className="p-2">
					<img
						src={proof}
						alt={asset_name}
						className="w-12"
					/>
				</td>
				<td className="py-2">
					<div className="flex gap-2">
						<button
							type="button"
							onClick={handleModal}
							className="p-2 transition-all duration-300 ease-in-out bg-green-100 border rounded-lg cursor-pointer text-main-primary border-main-primary hover:bg-main-primary hover:text-green-100 focus:outline-none">
							<ImPencil2 size={20} />
						</button>
					</div>
				</td>
			</tr>
		</tbody>
	)
}

export default HomeTableData
